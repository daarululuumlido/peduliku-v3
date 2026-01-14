import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

const STORAGE_KEY = 'peduliku-theme-settings'

// Preset color palettes
export const colorPresets = {
    indigo: { name: 'Indigo', primary: '#6366f1', secondary: '#a855f7' },
    blue: { name: 'Blue', primary: '#3b82f6', secondary: '#06b6d4' },
    emerald: { name: 'Emerald', primary: '#10b981', secondary: '#14b8a6' },
    rose: { name: 'Rose', primary: '#f43f5e', secondary: '#ec4899' },
    amber: { name: 'Amber', primary: '#f59e0b', secondary: '#eab308' },
    slate: { name: 'Slate', primary: '#64748b', secondary: '#475569' },
    violet: { name: 'Violet', primary: '#8b5cf6', secondary: '#a855f7' },
    cyan: { name: 'Cyan', primary: '#06b6d4', secondary: '#0ea5e9' },
}

// Font options from Google Fonts
export const fontOptions = [
    { name: 'Inter', family: "'Inter', sans-serif" },
    { name: 'Roboto', family: "'Roboto', sans-serif" },
    { name: 'Poppins', family: "'Poppins', sans-serif" },
    { name: 'Outfit', family: "'Outfit', sans-serif" },
    { name: 'Nunito', family: "'Nunito', sans-serif" },
    { name: 'Open Sans', family: "'Open Sans', sans-serif" },
    { name: 'Lato', family: "'Lato', sans-serif" },
    { name: 'Source Sans Pro', family: "'Source Sans 3', sans-serif" },
]

export const useThemeStore = defineStore('theme', () => {
    // State
    const primaryColor = ref('#6366f1')
    const secondaryColor = ref('#a855f7')
    const fontFamily = ref("'Inter', sans-serif")
    const fontName = ref('Inter')
    const darkMode = ref(false)
    const colorPresetKey = ref('indigo')
    const sidebarCollapsed = ref(false)

    // Computed
    const currentPreset = computed(() => colorPresets[colorPresetKey.value] || colorPresets.indigo)

    // Apply theme to CSS custom properties
    function applyTheme() {
        if (typeof document === 'undefined') return

        const root = document.documentElement
        root.style.setProperty('--theme-primary', primaryColor.value)
        root.style.setProperty('--theme-secondary', secondaryColor.value)
        root.style.setProperty('--theme-font', fontFamily.value)

        // Apply dark mode class
        if (darkMode.value) {
            document.documentElement.classList.add('dark')
            document.body.classList.add('dark-scrollbars')
        } else {
            document.documentElement.classList.remove('dark')
            document.body.classList.remove('dark-scrollbars')
        }
    }

    // Save to localStorage
    function saveToStorage() {
        if (typeof localStorage === 'undefined') return

        const settings = {
            primaryColor: primaryColor.value,
            secondaryColor: secondaryColor.value,
            fontFamily: fontFamily.value,
            fontName: fontName.value,
            darkMode: darkMode.value,
            colorPresetKey: colorPresetKey.value,
            sidebarCollapsed: sidebarCollapsed.value,
        }

        localStorage.setItem(STORAGE_KEY, JSON.stringify(settings))
    }

    // Load from localStorage
    function loadFromStorage() {
        if (typeof localStorage === 'undefined') return

        const saved = localStorage.getItem(STORAGE_KEY)
        if (saved) {
            try {
                const settings = JSON.parse(saved)
                primaryColor.value = settings.primaryColor || '#6366f1'
                secondaryColor.value = settings.secondaryColor || '#a855f7'
                fontFamily.value = settings.fontFamily || "'Inter', sans-serif"
                fontName.value = settings.fontName || 'Inter'
                darkMode.value = settings.darkMode || false
                colorPresetKey.value = settings.colorPresetKey || 'indigo'
                sidebarCollapsed.value = settings.sidebarCollapsed || false
            } catch (e) {
                console.error('Failed to parse theme settings:', e)
            }
        }
    }

    // Initialize theme
    function init() {
        loadFromStorage()
        applyTheme()

        // Load Google Font dynamically
        loadGoogleFont(fontName.value)
    }

    // Load Google Font
    function loadGoogleFont(name) {
        if (typeof document === 'undefined') return

        const existingLink = document.getElementById('google-font-link')
        if (existingLink) {
            existingLink.remove()
        }

        const link = document.createElement('link')
        link.id = 'google-font-link'
        link.rel = 'stylesheet'
        link.href = `https://fonts.googleapis.com/css2?family=${name.replace(/ /g, '+')}:wght@300;400;500;600;700&display=swap`
        document.head.appendChild(link)
    }

    // Set color preset
    function setColorPreset(key) {
        if (!colorPresets[key]) return

        colorPresetKey.value = key
        primaryColor.value = colorPresets[key].primary
        secondaryColor.value = colorPresets[key].secondary
        applyTheme()
        saveToStorage()
    }

    // Set primary color directly
    function setPrimaryColor(color) {
        primaryColor.value = color
        applyTheme()
        saveToStorage()
    }

    // Set secondary color directly
    function setSecondaryColor(color) {
        secondaryColor.value = color
        applyTheme()
        saveToStorage()
    }

    // Set font
    function setFont(name, family) {
        fontName.value = name
        fontFamily.value = family
        loadGoogleFont(name)
        applyTheme()
        saveToStorage()
    }

    // Toggle dark mode
    function toggleDarkMode() {
        darkMode.value = !darkMode.value
        applyTheme()
        saveToStorage()
    }

    // Toggle sidebar collapsed
    function toggleSidebar() {
        sidebarCollapsed.value = !sidebarCollapsed.value
        saveToStorage()
    }

    // Set sidebar collapsed explicitly
    function setSidebarCollapsed(value) {
        sidebarCollapsed.value = value
        saveToStorage()
    }

    // Set dark mode explicitly
    function setDarkMode(value) {
        darkMode.value = value
        applyTheme()
        saveToStorage()
    }

    // Reset to defaults
    function resetToDefaults() {
        primaryColor.value = '#6366f1'
        secondaryColor.value = '#a855f7'
        fontFamily.value = "'Inter', sans-serif"
        fontName.value = 'Inter'
        darkMode.value = false
        colorPresetKey.value = 'indigo'
        loadGoogleFont('Inter')
        applyTheme()
        saveToStorage()
    }

    return {
        // State
        primaryColor,
        secondaryColor,
        fontFamily,
        fontName,
        darkMode,
        colorPresetKey,
        sidebarCollapsed,
        // Computed
        currentPreset,
        // Methods
        init,
        setColorPreset,
        setPrimaryColor,
        setSecondaryColor,
        setFont,
        toggleDarkMode,
        setDarkMode,
        toggleSidebar,
        setSidebarCollapsed,
        resetToDefaults,
        applyTheme,
    }
})
