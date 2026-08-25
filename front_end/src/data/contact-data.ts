import { ContactItem } from "@/interFace/interFace";

export const contactData: ContactItem[] = [
    {
        icon: "fa-light fa-map-marker-alt",
        title: "New York Office",
        details: [
            "123 Fifth Avenue, NY 10160, USA",
            { text: "www.istudy.com", link: "https://www.istudy.com" }
        ]
    },
    {
        icon: "fa-light fa-phone",
        title: "Call Us",
        details: [
            "+1 (800) 123-4567",
            "+1 (800) 987-6543"
        ]
    },
    {
        icon: "fa-light fa-envelope",
        title: "Email Us",
        details: [
            { text: "info@istudy.com", link: "mailto:info@istudy.com" },
            { text: "support@istudy.com", link: "mailto:support@istudy.com" }
        ]
    },
    {
        icon: "fa-light fa-globe",
        title: "Visit Our Website",
        details: [
            { text: "www.istudy.com", link: "https://www.istudy.com" },
            { text: "www.istudy.info", link: "https://www.istudy.info" }
        ]
    }
];