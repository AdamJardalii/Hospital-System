import apiClient from "./api";

export const ScannerService = {
    async scanCard(file) {
        const formData = new FormData();
        formData.append("card_image", file);

        try {
            const response = await apiClient.post("/ocr/scan", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            return response.data.parsed_data.data;
        } catch (error) {
            console.error("Card scan error:", error);
            throw new Error(
                error.response?.data?.message || "Failed to scan card",
            );
        }
    },

    extractBloodType(otherArray) {
        if (!Array.isArray(otherArray)) return null;

        for (const item of otherArray) {
            if (
                typeof item === "string" &&
                item.toLowerCase().includes("blood")
            ) {
                const bloodTypeMatch = item.match(/([ABO]{1,2}[+-])/i);
                if (bloodTypeMatch) {
                    return bloodTypeMatch[1].toUpperCase();
                }
            }
        }
        return null;
    },
};
