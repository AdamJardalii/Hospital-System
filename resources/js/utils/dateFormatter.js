export const formatDateForDisplay = (isoDate) => {
    if (!isoDate) return "";
    const date = new Date(isoDate);
    return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};

export const formatDateForAPI = (inputDate) => {
    if (!inputDate) return "";
    const date = new Date(inputDate);
    const dd = String(date.getDate()).padStart(2, "0");
    const mm = String(date.getMonth() + 1).padStart(2, "0");
    const yyyy = date.getFullYear();
    return `${dd}-${mm}-${yyyy}`;
};

export const parseISOToInput = (isoDate) => {
    if (!isoDate) return "";
    return isoDate.split("T")[0];
};
