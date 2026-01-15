
from PIL import Image
from collections import Counter
import sys

def rgb_to_hex(rgb):
    return '#{:02x}{:02x}{:02x}'.format(rgb[0], rgb[1], rgb[2])

def get_dominant_colors(image_path, num_colors=5):
    try:
        image = Image.open(image_path)
        image = image.convert("RGB")
        image = image.resize((100, 100))
        
        pixels = list(image.getdata())
        
        # Filter out white-ish and black-ish pixels if needed, or just take top
        # For a logo, we usually want non-white background colors.
        # Simple filter: ignore pixels with high lightness or very low lightness?
        # Let's just count all and manually pick.
        filtered_pixels = []
        for r, g, b in pixels:
            # Ignore white background
            if r > 240 and g > 240 and b > 240:
                continue
            # Ignore transparent if alpha was handled, but we converted to RGB (white bg usually)
            filtered_pixels.append((r, g, b))
            
        counts = Counter(filtered_pixels)
        most_common = counts.most_common(num_colors)
        
        print("Dominant Colors:")
        for color, count in most_common:
            print(f"{rgb_to_hex(color)} (Count: {count})")
            
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    path = r"C:/Users/HasanBasri/.gemini/antigravity/brain/bde78086-a1f0-4b86-aae1-d71c8eff152f/uploaded_image_1768493139319.png"
    get_dominant_colors(path)
