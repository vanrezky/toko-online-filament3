import { computed, ref, onMounted, watch } from "vue";
import { usePage } from "@inertiajs/vue3";

const colorScheme = ref({
    primary: "#F97316",
    secondary: "#F5F3FC",
    accent: "#FB923C",
    destructive: "#F43F5E",
    background: "#FCFCFE",
    foreground: "#2D1B0E",
});

const applied = ref(false);

export function useColorScheme() {
    const page = usePage();

    const setColorScheme = (colors) => {
        if (colors) {
            colorScheme.value = {
                primary: colors.primary || "#F97316",
                secondary: colors.secondary || "#F5F3FC",
                accent: colors.accent || "#FB923C",
                destructive: colors.destructive || "#F43F5E",
                background: colors.background || "#FCFCFE",
                foreground: colors.foreground || "#2D1B0E",
            };
            applyColorScheme();
        }
    };

    const applyColorScheme = () => {
        const root = document.documentElement;
        root.style.setProperty("--color-primary", colorScheme.value.primary);
        root.style.setProperty("--color-secondary", colorScheme.value.secondary);
        root.style.setProperty("--color-accent", colorScheme.value.accent);
        root.style.setProperty("--color-destructive", colorScheme.value.destructive);
        root.style.setProperty("--color-background", colorScheme.value.background);
        root.style.setProperty("--color-foreground", colorScheme.value.foreground);

        const primaryRGB = hexToRgb(colorScheme.value.primary);
        const secondaryRGB = hexToRgb(colorScheme.value.secondary);
        const foregroundRGB = hexToRgb(colorScheme.value.foreground);

        root.style.setProperty("--color-primary-rgb", `${primaryRGB.r}, ${primaryRGB.g}, ${primaryRGB.b}`);
        root.style.setProperty("--color-secondary-rgb", `${secondaryRGB.r}, ${secondaryRGB.g}, ${secondaryRGB.b}`);
        root.style.setProperty("--color-foreground-rgb", `${foregroundRGB.r}, ${foregroundRGB.g}, ${foregroundRGB.b}`);

        applied.value = true;
    };

    const hexToRgb = (hex) => {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result
            ? {
                  r: parseInt(result[1], 16),
                  g: parseInt(result[2], 16),
                  b: parseInt(result[3], 16),
              }
            : { r: 0, g: 0, b: 0 };
    };

    const initColorScheme = () => {
        const pageColorScheme = page.props.colorScheme;
        if (pageColorScheme) {
            setColorScheme(pageColorScheme);
        } else if (!applied.value) {
            applyColorScheme();
        }
    };

    onMounted(() => {
        initColorScheme();
    });

    watch(
        () => page.props.colorScheme,
        (newScheme) => {
            if (newScheme) {
                setColorScheme(newScheme);
            }
        },
        { immediate: true },
    );

    return {
        colorScheme,
        setColorScheme,
        applyColorScheme,
    };
}
