import pricingThumb1 from "../../public/assets/images/price/pricing_thumb01.webp";
import pricingThumb2 from "../../public/assets/images/price/pricing_thumb02.webp";
import pricingThumb3 from "../../public/assets/images/price/pricing_thumb03.webp";
import checkIconSvg from "../../public/assets/images/icon/check.svg";
//pricing plan
import bookSvg from "../../public/assets/images/icon/book-3.svg";
import graduationSvg from "../../public/assets/images/icon/graduation.svg";
import gemSvg from "../../public/assets/images/icon/gem.svg";
import { PricingPlan } from "@/interFace/pricing-interface";


export const coursePricingPlanData = [
    //Online Course pricing plan data
    {
        id: 1,
        image: pricingThumb1,
        checkIcon: checkIconSvg,
        title: "Basic Plan",
        courses: 10,
        description: "Perfect for beginners, offering essential courses and community support.",
        features: [
            "Access to Free Courses",
            "Community Support",
            "1 Certificate",
            "Limited Resources",
        ],
        price: 15,
        url: "/pricing",
        hasBackground: false,
    },
    {
        id: 2,
        image: pricingThumb2,
        checkIcon: checkIconSvg,
        title: "Standard Plan",
        courses: 20,
        description: "Great for advancing skills with diverse courses and added resources.",
        features: [
            "Access to All Courses",
            "Priority Support",
            "3 Certificates",
            "Downloadable Resources",
        ],
        price: 30,
        url: "/pricing",
        hasBackground: true,
    },
    {
        id: 3,
        image: pricingThumb3,
        checkIcon: checkIconSvg,
        title: "Premium Plan",
        courses: "Unlimited",
        description: "Unlimited access with expert mentorship and exclusive perks.",
        features: [
            "All Access Pass",
            "1-on-1 Mentorship",
            "Unlimited Certificates",
            "Exclusive Webinars",
        ],
        price: 50,
        url: "/pricing",
        hasBackground: false,
    },
    //Online Course pricing plan data end
];

// Pricing plan data structure
export const pricingPlans: PricingPlan[] = [
    {
        id: 1,
        thumb: bookSvg,
        alt: "Education Pricing",
        title: "Starter",
        description: "Best for individual learners",
        monthly: { price: 19, duration: "/Month" },
        yearly: { price: 199, duration: "/Year" },
        buttonText: "Choose Starter Plan",
        buttonType: "outline-primary",
        features: [
            "Access to 50+ Courses",
            "Course Completion Certificates",
            "Self-Paced Learning",
            "Basic Customer Support",
        ],
    },
    {
        id: 2,
        badge: "Popular",
        thumb: graduationSvg,
        alt: "Education Pricing",
        title: "Professional",
        description: "Ideal for career-focused learners",
        monthly: { price: 39, duration: "/Month" },
        yearly: { price: 429, duration: "/Year" },
        buttonText: "Choose Professional Plan",
        buttonType: "primary",
        features: [
            "Access to 200+ Courses",
            "Live Classes & Webinars",
            "Advanced Certifications",
            "Priority Customer Support",
        ],
    },
    {
        id: 3,
        thumb: gemSvg,
        alt: "Education Pricing",
        title: "Institutional",
        description: "Designed for schools and organizations",
        monthly: { price: 99, duration: "/Month" },
        yearly: { price: 1099, duration: "/Year" },
        buttonText: "Choose Institutional Plan",
        buttonType: "outline-primary",
        features: [
            "Access to All Courses",
            "Dedicated Account Manager",
            "Custom Learning Paths",
            "24/7 Support",
        ],
    },
];