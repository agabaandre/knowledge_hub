import { IEvent } from "@/interFace/interFace";
import eventImg1 from "../../public/assets/images/event/event-img-1.webp";
import eventImg2 from "../../public/assets/images/event/event-img-2.webp";
import eventImg3 from "../../public/assets/images/event/event-img-3.webp";
import eventImg4 from "../../public/assets/images/event/event-img-4.webp";
import eventImg5 from "../../public/assets/images/event/event-img-5.webp";
import eventImg6 from "../../public/assets/images/event/event-img-6.webp";
import eventThumb1 from "../../public/assets/images/event/event-thumb-09.webp";
import eventThumb2 from "../../public/assets/images/event/event-thumb-06.webp";
import eventThumb3 from "../../public/assets/images/event/event-thumb-07.webp";
import eventThumb4 from "../../public/assets/images/event/event-thumb-08.webp";
import eventThumb5 from "../../public/assets/images/event/event-thumb-09.webp";

// eventsData.js
export const eventData: IEvent[] = [
    //university event data start
    {
        id: 1,
        date: "10",
        monthYear: "April, 2024",
        location: "London, US",
        time: "8.00 am - 6.00 pm",
        title: "Week-long Events Welcoming New Students, Including Tours",
        isActive: false,
    },
    {
        id: 2,
        date: "09",
        monthYear: "March, 2024",
        location: "London, US",
        time: "8.00 am - 6.00 pm",
        title: "An Event Where Students Can Showcase Their Oratory and Analytical Skills",
        isActive: true,
    },
    {
        id: 3,
        date: "15",
        monthYear: "Feb, 2024",
        location: "London, US",
        time: "8.00 am - 6.00 pm",
        title: "An Event Where Students Can Meet With Potential Employers Explore Job",
        isActive: false,
    },
    //University event data end
    //program details event data
    {
        id: 4,
        image: eventImg1,
        date: "20",
        monthYear: "Sep",
        location: "Online",
        time: "9:00am - 5:00pm",
        title: "Python Programming",
        description: "Learn the basics of Python programming in this comprehensive online course.",
    },
    {
        id: 5,
        image: eventImg2,
        date: "25",
        monthYear: "Sep",
        location: "Online",
        time: "3:00pm - 4:00pm",
        title: "University Admission Webinar",
        description: "Join our webinar to learn more about the university admission process and tips.",
    },
    {
        id: 6,
        image: eventImg3,
        date: "01",
        monthYear: "Oct",
        location: "Online",
        time: "10:00am - 1:00pm",
        title: "Modern Teaching Strategies",
        description: "Explore innovative teaching methods and tools for modern classrooms.",
    },

    //event data start
    {
        id: 7,
        image: eventImg1,
        date: "20",
        monthYear: "Sep",
        location: "Online",
        time: "9:00am - 5:00pm",
        title: "Python Programming",
        description: "Learn the basics of Python programming in this comprehensive online course.",
    },
    {
        id: 8,
        image: eventImg2,
        date: "25",
        monthYear: "Sep",
        location: "Online",
        time: "3:00pm - 4:00pm",
        title: "University Admission Webinar",
        description: "Join our webinar to learn more about the university admission process and tips.",
    },
    {
        id: 9,
        image: eventImg3,
        date: "01",
        monthYear: "Oct",
        location: "Online",
        time: "10:00am - 1:00pm",
        title: "Modern Teaching Strategies",
        description: "Explore innovative teaching methods and tools for modern classrooms.",
    },
    {
        id: 10,
        image: eventImg4,
        date: "10",
        monthYear: "Sep",
        location: "Online",
        time: "9:00am - 5:00pm",
        title: "Kindergarten Creativity Workshop",
        description: "Advanced course on data science techniques and tools for professionals.",
    },
    {
        id: 11,
        image: eventImg5,
        date: "15",
        monthYear: "Sep",
        location: "Online",
        time: "8:00am - 4:00pm",
        title: "Advanced Data Science",
        description: "Advanced course on data science techniques and tools for professionals.",
    },
    {
        id: 12,
        image: eventImg6,
        date: "22",
        monthYear: "Oct",
        location: "Online",
        time: "9:00am - 3:00pm",
        title: "University Open Day",
        description: "Visit our campus and meet faculty members to learn more about our programs.",
    },
    //event data end
    //event-list page data start
    {
        id: 13,
        image: eventThumb1,
        date: "25 Sep 2024",
        monthYear: "Sep",
        location: "Online",
        time: "9:00am - 5:00pm",
        title: "Intro to Web Development: A Comprehensive Guide",
        description: "Learn the basics of Python programming in this comprehensive online course.",
    },
    {
        id: 14,
        image: eventThumb2,
        date: "02 Oct 2024",
        monthYear: "Sep",
        location: "Online",
        time: "3:00pm - 4:30pm",
        title: "University Application Workshop: Tips and Tricks",
        description: "Join our webinar to learn more about the university admission process and tips.",
    },
    {
        id: 15,
        image: eventThumb3,
        date: "10 Oct 2024",
        monthYear: "Oct",
        location: "Online",
        time: "11:00am - 2:00pm",
        title: "Modern Pedagogy: Innovative Teaching Methods",
        description: "Explore innovative teaching methods and tools for modern classrooms.",
    },
    {
        id: 16,
        image: eventThumb4,
        date: "18 Oct 2024",
        monthYear: "Sep",
        location: "Community Center",
        time: "1:00pm - 3:00pm",
        title: "Kindergarten Readiness: Essential Skills for Kids",
        description: "Advanced course on data science techniques and tools for professionals.",
    },
    {
        id: 17,
        image: eventThumb5,
        date: "25 Oct 2024",
        monthYear: "Sep",
        location: "Online",
        time: "10:00am - 5:00pm",
        title: "Introduction to Data Science: Online Course",
        description: "Advanced course on data science techniques and tools for professionals.",
    },
    //event-list data end
    //kindergarten event data start
    {
        id: 18,
        image: eventThumb3,
        date: "15 Aug 2024",
        monthYear: "Oct",
        location: "Online",
        time: "11:00am - 2:00pm",
        title: "Celebrating Creativity The Tiny Tot Talent Showcase Extravaganza",
        description: "Celebrating Creativity The Tiny Tot Talent Showcase Extravaganza",
    },
    {
        id: 19,
        image: eventThumb4,
        date: "15 Aug 2024",
        monthYear: "Sep",
        location: "Community Center",
        time: "1:00pm - 3:00pm",
        title: "Art in the Park A Day of Outdoor Creativity and Imagination",
    },
    {
        id: 20,
        image: eventThumb5,
        date: "15 Aug 2024",
        monthYear: "Sep",
        location: "Online",
        time: "10:00am - 5:00pm",
        title: "The Kindergarten Carnival Spectacular A Day of Games, Rides, and Joy",
    },
];
