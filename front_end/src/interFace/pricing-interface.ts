import { StaticImageData } from "next/image";

// Component for a single pricing plan card
export interface PricingPlan {
    id: number;
    badge?: string;
    thumb: StaticImageData;
    alt: string;
    title: string;
    description: string;
    yearly: { price: number; duration: string };
    monthly: { price: number; duration: string };
    buttonText: string;
    buttonType: "primary" | "outline-primary";
    features: string[];
}

export interface PricingPlanCardProps {
    plan: PricingPlan;
    isYearly: boolean;
}