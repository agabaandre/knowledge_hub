interface Lecture {
    title: string;
    duration: string;
}

interface ICurriculam {
    title: string;
    lectures: Lecture[];
}

const curriculamData: ICurriculam[] = [
    {
        title: "Introduction to Web Development",
        lectures: [
            { title: "Overview of HTML, CSS, and JavaScript", duration: "08:45" },
            { title: "Setting up your development environment", duration: "10:22" },
        ],
    },
    {
        title: "Building Your First Web Page",
        lectures: [
            { title: "Creating and structuring HTML elements", duration: "12:30" },
            { title: "Styling your page with CSS", duration: "15:10" },
        ],
    },
    {
        title: "JavaScript Basics",
        lectures: [
            { title: "Introduction to JavaScript syntax", duration: "18:30" },
            { title: "Working with variables and data types", duration: "14:45" },
        ],
    },
    {
        title: "JavaScript DOM Manipulation",
        lectures: [
            { title: "Selecting and modifying elements", duration: "16:20" },
            { title: "Event handling in JavaScript", duration: "11:45" },
        ],
    },
    {
        title: "Advanced JavaScript: ES6+",
        lectures: [
            { title: "Arrow functions and template literals", duration: "12:00" },
            { title: "Modules, classes, and inheritance", duration: "13:25" },
        ],
    },
    {
        title: "React Basics",
        lectures: [
            { title: "Introduction to React components", duration: "18:50" },
            { title: "State management in React", duration: "16:40" },
        ],
    },
    {
        title: "Full-Stack Development with Node.js",
        lectures: [
            { title: "Building REST APIs with Express", duration: "20:10" },
            { title: "Connecting to MongoDB using Mongoose", duration: "22:35" },
        ],
    },
];

export default curriculamData;