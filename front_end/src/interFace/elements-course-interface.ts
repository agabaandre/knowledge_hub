import { StaticImageData } from "next/image";

export interface IElementCourse {
    id: number;
    image: StaticImageData;
    title: string;
    badgeText?: string;
    badgeColor?: string;
    rating: number;
    price: number | string;
    oldPrice?: number;
    authorName: string;
    lessons: number;
    bgImage?: StaticImageData;
    logo?: StaticImageData;
    level?: string;
    lavelTwo?: string;
    lavelThree?: string;
    tag?: string;
    reviews?: number;
    authorImage?: StaticImageData;
    courseTextContent?: boolean;
    isCourseThreeStyled?: boolean;
    imageBgClass?: string;
    instructorImagePosition?: string;
    courseTitleColor?: string;
    FontSizeClass?: string;
    spacingClass?: string;
    students?: number;
    courseDescription?: string;
    quantity?: number;
    certificateBadge?: string;
    smallText?: string;
    details?: string;
    courseTitle?:string;
    courseList?: string[];
}