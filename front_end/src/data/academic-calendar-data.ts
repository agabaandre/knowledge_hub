import { IAcademicCalendar } from "@/interFace/interFace";
import fallSemisterImg from '../../public/assets/images/calendar/fall-semester.webp';
import springSemisterImg from '../../public/assets/images/calendar/spring-semester.webp';
import summerSemisterImg from '../../public/assets/images/calendar/summer-semester.webp';

export const academicCalendarData: IAcademicCalendar[] = [
    {
        semester: "Fall Semester",
        image: fallSemisterImg,
        events: [
            { label: "Classes Begin", date: "Aug 30, 2024" },
            { label: "Last Day to Add/Drop Classes", date: "September 6, 2024" },
            { label: "Labor Day (No Classes)", date: "September 2, 2024" },
            { label: "Midterm Exams", date: "October 14 - October 18, 2024" },
            { label: "Thanksgiving Break", date: "November 27 - November 29, 2024" },
            { label: "Last Day of Classes", date: "December 6, 2024" },
            { label: "Final Exams", date: "December 10 - December 14, 2024" },
            { label: "Winter Break", date: "December 15, 2024 - Jan 5, 2025" },
        ],
    },
    {
        semester: "Spring Semester",
        image: springSemisterImg,
        events: [
            { label: "Classes Begin", date: "Jan 8, 2025" },
            { label: "Last Day to Add/Drop Classes", date: "Jan 15, 2025" },
            { label: "Martin Luther King Jr. Day (No Classes)", date: "Jan 20, 2025" },
            { label: "Midterm Exams", date: "March 3 - March 7, 2025" },
            { label: "Spring Break", date: "March 24 - March 28, 2025" },
            { label: "Last Day of Classes", date: "May 2, 2025" },
            { label: "Final Exams", date: "May 5 - May 9, 2025" },
            { label: "Commencement", date: "May 15, 2025" },
        ],
    },
    {
        semester: "Summer Semester",
        image: summerSemisterImg,
        events: [
            { label: "Classes Begin", date: "May 12, 2025" },
            { label: "Last Day to Add/Drop Classes", date: "May 19, 2025" },
            { label: "Memorial Day (No Classes)", date: "May 26, 2025" },
            { label: "Final Exams", date: "June 30 - July 4, 2025" },
            { label: "Last Day of Classes", date: "July 5, 2025" },
            { label: "Summer Break", date: "July 6 - Aug 15, 2025" },
        ],
    },
];