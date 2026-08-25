import ArtsDesignSvg from "@/svg/categories/modern-schooling/ArtsDesignSvg";
import BusinessAnalysisSvg from "@/svg/categories/modern-schooling/BusinessAnalysisSvg";
import CookingBakingSvg from "@/svg/categories/modern-schooling/CookingBakingSvg";
import DanceMusicSvg from "@/svg/categories/modern-schooling/DanceMusicSvg";
import DataScienceSvg from "@/svg/categories/modern-schooling/DataScienceSvg";
import DigitalMarketingSvg from "@/svg/categories/modern-schooling/DigitalMarketingSvg";
import FinanceSvg from "@/svg/categories/modern-schooling/FinanceSvg";
import HealthFitnessSvg from "@/svg/categories/modern-schooling/HealthFitnessSvg";
import PhotographySvg from "@/svg/categories/modern-schooling/PhotographySvg";
import PsychologySvg from "@/svg/categories/modern-schooling/PsychologySvg";
import RoboticsAISvg from "@/svg/categories/modern-schooling/RoboticsAISvg";
import WebDevelopmentSvg from "@/svg/categories/modern-schooling/WebDevelopmentSvg";

// Define an interface for the categories data
interface IElementCategories {
    id: number;
    title?: string;
    totalCourses?: number;
    icon?: React.ComponentType<React.SVGProps<SVGSVGElement>>;
    categories?: IElementCategories[];
}
const elementCategoriesData: IElementCategories[] = [
    //categories style data 01 start
    {
        id: 1,
        title: "Graphics Design",
        totalCourses: 30,
        icon: ArtsDesignSvg
    },
    {
        id: 2,
        title: "Web Development",
        totalCourses: 20,
        icon: WebDevelopmentSvg
    },
    {
        id: 3,
        title: "Photography",
        totalCourses: 25,
        icon: PhotographySvg
    },
    {
        id: 4,
        title: "Business Analysis",
        totalCourses: 35,
        icon: BusinessAnalysisSvg
    },
    {
        id: 5,
        title: "Digital Marketing",
        totalCourses: 40,
        icon: DigitalMarketingSvg
    },
    {
        id: 6,
        title: "Data Science",
        totalCourses: 15,
        icon: DataScienceSvg
    },
    {
        id: 7,
        title: "Health & Fitness",
        totalCourses: 10,
        icon: HealthFitnessSvg
    },
    {
        id: 8,
        title: "Dance & Music",
        totalCourses: 25,
        icon: DanceMusicSvg
    },
    //categories style data 02 start
    {
        id: 9,
        categories: [
            {
                id: 1,
                title: "Arts & Design",
                totalCourses: 11,
                icon: ArtsDesignSvg,
            },
            {
                id: 2,
                title: "Web Development",
                totalCourses: 13,
                icon: WebDevelopmentSvg,
            },
        ]
    },
    {
        id: 10,
        categories: [
            {
                id: 3,
                title: "Photography",
                totalCourses: 10,
                icon: PhotographySvg,
            },
            {
                id: 4,
                title: "Business Analysis",
                totalCourses: 12,
                icon: BusinessAnalysisSvg,
            },
        ]
    },
    {
        id: 11,
        categories: [
            {
                id: 5,
                title: "Digital Marketing",
                totalCourses: 17,
                icon: DigitalMarketingSvg,
            },
            {
                id: 6,
                title: "Data Science",
                totalCourses: 15,
                icon: DataScienceSvg,
            },
        ]
    },
    {
        id: 12,
        categories: [
            {
                id: 7,
                title: "Health & Fitness",
                totalCourses: 12,
                icon: HealthFitnessSvg,
            },
            {
                id: 8,
                title: "Dance & Music",
                totalCourses: 14,
                icon: DanceMusicSvg,
            },
        ]
    },
    {
        id: 13,
        categories: [
            {
                id: 9,
                title: "Robotics & AI",
                totalCourses: 11,
                icon: RoboticsAISvg,
            },
            {
                id: 10,
                title: "Psychology",
                totalCourses: 15,
                icon: PsychologySvg,
            }
        ]
    },
    {
        id: 14,
        categories: [
            {
                id: 11,
                title: "Cooking & Baking",
                totalCourses: 13,
                icon: CookingBakingSvg,
            },
            {
                id: 12,
                title: "Finance",
                totalCourses: 10,
                icon: FinanceSvg,
            }
        ]
    }
]
export default elementCategoriesData;