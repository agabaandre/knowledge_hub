import { ICategories, ISchoolinCategories } from "@/interFace/interFace";
// Importing the SVG components
import ArtsDesignSvg from "@/svg/categories/modern-schooling/ArtsDesignSvg";
import WebDevelopmentSvg from "@/svg/categories/modern-schooling/WebDevelopmentSvg";
import PhotographySvg from "@/svg/categories/modern-schooling/PhotographySvg";
import BusinessAnalysisSvg from "@/svg/categories/modern-schooling/BusinessAnalysisSvg";
import DigitalMarketingSvg from "@/svg/categories/modern-schooling/DigitalMarketingSvg";
import DataScienceSvg from "@/svg/categories/modern-schooling/DataScienceSvg";
import HealthFitnessSvg from "@/svg/categories/modern-schooling/HealthFitnessSvg";
import DanceMusicSvg from "@/svg/categories/modern-schooling/DanceMusicSvg";
import RoboticsAISvg from "@/svg/categories/modern-schooling/RoboticsAISvg";
import PsychologySvg from "@/svg/categories/modern-schooling/PsychologySvg";
import CookingBakingSvg from "@/svg/categories/modern-schooling/CookingBakingSvg";
import FinanceSvg from "@/svg/categories/modern-schooling/FinanceSvg";

export const categoriesData: ICategories[] = [
    //Course Online categories data start
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
    }
];
//Modern Schooling Categories data
export const schoolingCategoriesData: ISchoolinCategories[] = [
    {
        id: 1,
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
        id: 2,
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
        id: 3,
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
        id: 4,
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
        id: 5,
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
        id: 6,
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

export const shopCategoriesData = [
    {
        id: 1,
        title: 'Biography',
        icon: 'fa-solid fa-user',
        color: 'color-primary',
        totalBooks: '1000+ Books',
    },
    {
        id: 2,
        title: 'Fiction',
        icon: 'fa-solid fa-book-open',
        color: 'color-secondary',
        totalBooks: '1200+ Books',
    },
    {
        id: 3,
        title: 'History',
        icon: 'fa-solid fa-landmark',
        color: 'color-extra-01',
        totalBooks: '1350+ Books',
    },
    {
        id: 4,
        title: 'Children',
        icon: 'fa-solid fa-child',
        color: 'color-extra-02',
        totalBooks: '500+ Books',
    },
    {
        id: 5,
        title: 'Science',
        icon: 'fa-solid fa-flask',
        color: 'color-extra-03',
        totalBooks: '950+ Books',
    },
    {
        id: 6,
        title: 'Romance',
        icon: 'fa-solid fa-heart',
        color: 'color-extra-04',
        totalBooks: '999+ Books',
    },
    {
        id: 7,
        title: 'Fantasy',
        icon: 'fa-solid fa-hat-wizard',
        color: 'color-extra-05',
        totalBooks: '1500+ Books',
    },
    {
        id: 8,
        title: 'Mystery',
        icon: 'fa-solid fa-mask',
        color: 'color-extra-06',
        totalBooks: '450+ Books',
    },
]

// Example course category filter data
export const courseCategoriesData = [
    {checkId: "1", name: "Data Science", count: 20, isChecked: true },
    {checkId: "2", name: "Machine Learning", count: 18, isChecked: false },
    {checkId: "3", name: "Computer Science", count: 25, isChecked: false },
    {checkId: "4", name: "Business Administration", count: 12, isChecked: false },
];
// course top filter data
export const courseCategoriesDataTwo = [
    { checkId: "5", name: "Data Science", count: 20, isChecked: true },
    { checkId: "6", name: "Machine Learning", count: 18, isChecked: false },
    { checkId: "7", name: "Computer Science", count: 25, isChecked: false },
    { checkId: "8", name: "Business Administration", count: 12, isChecked: false },
    { checkId: "9", name: "Finance", count: 10, isChecked: false },
    { checkId: "10", name: "Marketing", count: 14, isChecked: false },
    { checkId: "11", name: "Graphic Design", count: 9, isChecked: false },
    { checkId: "12", name: "Psychology", count: 11, isChecked: false },
    { checkId: "13", name: "Engineering", count: 16, isChecked: false },
    { checkId: "14", name: "Language Learning", count: 13, isChecked: false },
];
// course sidebar filter data
export const courseCategoriesDataTwoThree = [
    { checkId: "15", name: "Data Science", count: 20, isChecked: true },
    { checkId: "16", name: "Machine Learning", count: 18, isChecked: false },
    { checkId: "17", name: "Computer Science", count: 25, isChecked: false },
    { checkId: "18", name: "Business Administration", count: 12, isChecked: false },
    { checkId: "19", name: "Finance", count: 10, isChecked: false },
    { checkId: "20", name: "Marketing", count: 14, isChecked: false },
    { checkId: "21", name: "Graphic Design", count: 9, isChecked: false },
    { checkId: "22", name: "Psychology", count: 11, isChecked: false },
    { checkId: "23", name: "Engineering", count: 16, isChecked: false },
    { checkId: "24", name: "Language Learning", count: 13, isChecked: false },
];
// Quran Learning Categories Data
export const quranLearningCategories = [
    { title: "Quranic Tafsir", icon: "fas fa-book-quran", color: "color-primary" },
    { title: "Hadith Studies", icon: "fas fa-scroll", color: "color-secondary" },
    { title: "Islamic Fiqh", icon: "fas fa-scale-balanced", color: "color-extra-01" },
    { title: "Arabic Language", icon: "fas fa-language", color: "color-extra-02" },
    { title: "Quran Memorization", icon: "fas fa-moon", color: "color-extra-05" },
    { title: "Islamic History", icon: "fas fa-landmark", color: "color-extra-06" },
    { title: "Seerah of Prophet Muhammad", icon: "fas fa-user-tie", color: "color-extra-07" },
    { title: "Islamic Ethics and Morality", icon: "fas fa-heart", color: "color-extra-08" },
];