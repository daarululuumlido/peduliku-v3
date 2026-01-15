
import re
import sys

input_path = r"docs\fase\struk.md"
output_path = r"docs\fase\struk_clean.md"

def parse_struk():
    try:
        with open(input_path, 'r', encoding='utf-8') as f:
            lines = f.readlines()
    except Exception as e:
        print(f"Error reading file: {e}")
        return

    md_lines = []
    
    # Frontmatter
    md_lines.append("---")
    md_lines.append("id: struk")
    md_lines.append("title: Struktur Organisasi")
    md_lines.append("sidebar_position: 10")
    md_lines.append("---")
    md_lines.append("")
    md_lines.append("# Struktur Organisasi Pesantren Modern Daarul Uluum Lido")
    md_lines.append("**Tahun Pelajaran 2025 - 2026**")
    md_lines.append("")

    table_buffer = []
    
    def flush_table():
        if not table_buffer:
            return
        
        md_lines.append("| Jabatan | Pejabat | ID |")
        md_lines.append("|---|---|---|")
        for row in table_buffer:
            md_lines.append(f"| {row[0]} | {row[1]} | `{row[2]}` |")
        md_lines.append("")
        table_buffer.clear()

    # Skip header
    start_idx = 0
    for i, line in enumerate(lines):
        if line.strip().startswith('A'): # Detect start of content
            start_idx = i
            break
            
    content_lines = lines[start_idx:]

    for line in content_lines:
        clean_line = line.strip()
        if not clean_line:
            continue
            
        # Split by tabs
        raw_parts = re.split(r'\t+', clean_line)
        # Filter empty strings but keep order
        parts = [p.strip() for p in raw_parts if p.strip()]
        
        if not parts:
            continue

        # Check for ID (column that is digit only, 2-4 chars), search backwards
        id_idx = -1
        person_id = ""
        for i in range(len(parts) - 1, -1, -1):
            if re.match(r'^\d{2,4}$', parts[i]):
                # Heuristic: If index is 0, and followed by text, and part is < 20, assume it is line number not ID
                # Unless there are no other parts
                if i == 0 and len(parts) > 1 and parts[i].isdigit() and int(parts[i]) < 100:
                     continue
                
                id_idx = i
                person_id = parts[i]
                break
        
        # HEADERS DETECTION
        first = parts[0]
        
        # CASE 1: SECTION HEADER (A, B, C...)
        if len(first) <= 2 and first[0].isalpha() and first.isupper() and (len(first)==1 or first[1]=='.'):
            flush_table()
            # Title is usually the second part
            title = parts[1] if len(parts) > 1 else ""
            if len(parts) > 2 and parts[2] == ":": # Sometimes colon is separate
                 title = parts[1] # Keep it
            
            # If line has ID, it's a Header AND a Row
            md_lines.append(f"## {first} {title}")
            md_lines.append("")
            
            if id_idx != -1:
                # Add to table
                # Name is after ID
                name = parts[id_idx+1] if len(parts) > id_idx+1 else ""
                # Role is between header and colon? OR just the title?
                # Usually: A Header : Role ID Name
                # Let's just use the title as role for the table
                row_role = title
                # If there is a colon, maybe the text after colon is the specific role
                # Search for part that equals title to avoid redundancy?
                
                # Let's try to extract text between ':' and ID
                # This is hard to do perfectly generic, let's just take the part before ID
                role_candidate = parts[id_idx-1] if id_idx > 0 else title
                if role_candidate == ":":
                     role_candidate = parts[id_idx-2] if id_idx > 1 else title
                
                table_buffer.append([role_candidate, name, person_id])
            continue

        # CASE 2: SUBSECTION HEADER (1, 2, 3...)
        if re.match(r'^\d{1,2}$', first):
            flush_table()
            title = parts[1] if len(parts) > 1 else ""
            md_lines.append(f"### {first}. {title}")
            md_lines.append("")
            
            if id_idx != -1:
                name = parts[id_idx+1] if len(parts) > id_idx+1 else ""
                role_candidate = parts[id_idx-1] if id_idx > 0 else title
                if role_candidate == ":":
                     role_candidate = parts[id_idx-2] if id_idx > 1 else title
                table_buffer.append([role_candidate, name, person_id])
            continue
            
        # CASE 3: Normal Row
        if id_idx != -1:
            # It's a person row
            name = parts[id_idx+1] if len(parts) > id_idx+1 else ""
            
            # Role finding
            # If line has ':', take text to the right of ':' and left of ID
            # If not, take text left of ID
            
            role = ""
            # Reconstruct line to find positions
            # This is tricky with split list.
            # Strategy: Grab the part immediately preceding ID.
            
            prev = parts[id_idx-1]
            if prev == ":":
                prev = parts[id_idx-2]
            
            role = prev
            
            # If Role is same as Name (unlikely) or empty
            if not role: 
                role = "Staff"
            
            table_buffer.append([role, name, person_id])
        else:
            # Text line without ID?
            # Could be a sub-header without number
            # "Div. Teknologi Informatika :"
            clean_text = clean_line.replace(':', '').strip()
            # Deduplicate repeated text (e.g. "Div. X   Div. X")
            # simple check: split by whitespace
            # actually, just checking if strict half repetition
            mid = len(clean_text) // 2
            # Very naive check for "Text Text"
            if clean_text[:mid].strip() == clean_text[mid:].strip():
                 clean_text = clean_text[:mid].strip()
            
            # Alternative: Split by >= 2 spaces
            text_parts = re.split(r'\s{2,}', clean_text)
            if len(text_parts) > 1 and text_parts[0] == text_parts[1]:
                 clean_text = text_parts[0]

            if "Div." in first or "Unit" in first or "Bagian" in first:
                 flush_table()
                 md_lines.append(f"**{clean_text}**")
                 md_lines.append("")
            else:
                 # Just add to buffer as a note or ignore?
                 pass

    flush_table() # Final flush

    with open(output_path, 'w', encoding='utf-8') as f:
        f.write("\n".join(md_lines))
    print("Done")

if __name__ == "__main__":
    parse_struk()
