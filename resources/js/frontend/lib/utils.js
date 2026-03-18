import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs) {
    return twMerge(clsx(inputs));
}

export function valueUpdater(updaterOrValue, ref) {
    ref.value = typeof updaterOrValue === "function" ? updaterOrValue(ref.value) : updaterOrValue;
}

export function assets(pathfile) {
    return `${import.meta.env.BASE_URL}${pathfile}`;
}

export function formatCurrency(amount, currency = "IDR") {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: currency,
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}

export function formatPrice(amount) {
    return formatCurrency(amount).replace("Rp", "").trim();
}
