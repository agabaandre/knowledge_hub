import { ICurriculumData } from "@/interFace/curriculumTypes";

const curriculumData: ICurriculumData = [
    {
        year: "First Year",
        semesters: [
            {
                title: "1st Year 1st Semester",
                courses: [
                    { sl: 1, code: "Math 101", title: "Mathematics I", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 2, code: "Phy 102", title: "Physics I", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 3, code: "Chem 103", title: "Chemistry", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 4, code: "CE 104", title: "Basic Surveying and Introduction to GIS", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 5, code: "CE 105", title: "Civil Engineering Drawing", theoryHours: null, theoryCredit: null, sessionalHours: 3.00, sessionalCredit: 1.50, totalCredit: 1.50 }
                ],
                total: { theoryHours: 12.0, theoryCredit: 12.0, sessionalHours: 3.0, sessionalCredit: 1.50, totalCredit: 13.50 }
            },
            {
                title: "1st Year 2nd Semester",
                courses: [
                    { sl: 1, code: "Math 201", title: "Mathematics II", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 2, code: "Phy 202", title: "Physics II", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 3, code: "Hum 203", title: "English", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 4, code: "CE 204", title: "Engineering Mechanics", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 5, code: "CE 205", title: "Computer Aided Drawing", theoryHours: null, theoryCredit: null, sessionalHours: 3.00, sessionalCredit: 2.50, totalCredit: 2.50 }
                ],
                total: { theoryHours: 12.0, theoryCredit: 12.0, sessionalHours: 3.0, sessionalCredit: 2.50, totalCredit: 14.50 }
            }
        ],
    },
    {
        year: "Second Year",
        semesters: [
            {
                title: "2nd Year 1st Semester",
                courses: [
                    { sl: 1, code: "Math 301", title: "Mathematics III", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 2, code: "CE 302", title: "Mechanics of Solids I", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 3, code: "Hum 303", title: "English", theoryHours: 2.0, theoryCredit: 2.00, sessionalHours: null, sessionalCredit: null, totalCredit: 2.00 },
                    { sl: 4, code: "CE 304", title: "Engineering Materials", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 5, code: "CE 305", title: "Engineering Materials Sessional", theoryHours: null, theoryCredit: null, sessionalHours: 3.00, sessionalCredit: 1.50, totalCredit: 1.50 }
                ],
                total: { theoryHours: 11.0, theoryCredit: 12.0, sessionalHours: 3.0, sessionalCredit: 1.50, totalCredit: 12.50 }
            },
            {
                title: "2nd Year 2nd Semester",
                courses: [
                    { sl: 1, code: "CE 401", title: "Numerical Analysis", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 2, code: "CE 402", title: "Mechanics of Solids II", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 3, code: "CE 403", title: "Geology and Geomorphology", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 4, code: "CE 404", title: "Fluid Mechanics", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 5, code: "CE 405", title: "Details of Construction", theoryHours: null, theoryCredit: null, sessionalHours: 3.00, sessionalCredit: 1.50, totalCredit: 1.50 }
                ],
                total: { theoryHours: 12.0, theoryCredit: 12.0, sessionalHours: 3.0, sessionalCredit: 1.50, totalCredit: 13.50 }
            }
        ]
    },
    {
        year: "Third Year",
        semesters: [
            {
                title: "3rd Year 1st Semester",
                courses: [
                    { sl: 1, code: "CE 501", title: "Structural Analysis and Design I", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 2, code: "CE 502", title: "Design of Concrete Structures I", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 3, code: "CE 503", title: "Geotechnical Engineering I", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 4, code: "CE 504", title: "Environmental Engineering I", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 5, code: "CE 505", title: "Open Channel Hydraulics", theoryHours: null, theoryCredit: null, sessionalHours: 3.00, sessionalCredit: 1.50, totalCredit: 1.50 }
                ],
                total: { theoryHours: 12.0, theoryCredit: 12.0, sessionalHours: 3.0, sessionalCredit: 1.50, totalCredit: 13.50 }
            },
            {
                title: "3rd Year 2nd Semester",
                courses: [
                    { sl: 1, code: "CE 601", title: "Design of Concrete Structures II", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 2, code: "CE 602", title: "Environmental Engineering II", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 3, code: "CE 603", title: "Transportation Engineering I", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 4, code: "CE 604", title: "Hydrology, Irrigation and Flood Control", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 5, code: "CE 605", title: "Building Design Sessional", theoryHours: null, theoryCredit: null, sessionalHours: 3.00, sessionalCredit: 1.50, totalCredit: 1.50 }
                ],
                total: { theoryHours: 12.0, theoryCredit: 12.0, sessionalHours: 3.0, sessionalCredit: 1.50, totalCredit: 13.50 }
            }
        ]
    },
    {
        year: "Fourth Year",
        semesters: [
            {
                title: "4th Year 1st Semester",
                courses: [
                    { sl: 1, code: "CE 401", title: "Design of Steel Structures II", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 2, code: "CE 402", title: "Foundation Engineering", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 3, code: "CE 403", title: "Environmental Engineering II", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 4, code: "CE 404", title: "Transportation Engineering II", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 5, code: "CE 405", title: "Project and Thesis", theoryHours: null, theoryCredit: null, sessionalHours: 6.00, sessionalCredit: 3.00, totalCredit: 3.00 }
                ],
                total: { theoryHours: 12.0, theoryCredit: 12.0, sessionalHours: 6.0, sessionalCredit: 3.00, totalCredit: 15.00 }
            },
            {
                title: "4th Year 2nd Semester",
                courses: [
                    { sl: 1, code: "CE 406", title: "Construction Management and Laws", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 2, code: "CE 407", title: "Hydrology and Water Resources Engineering", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 3, code: "CE 408", title: "Elective I", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 4, code: "CE 409", title: "Elective II", theoryHours: 3.0, theoryCredit: 3.00, sessionalHours: null, sessionalCredit: null, totalCredit: 3.00 },
                    { sl: 5, code: "CE 410", title: "Project and Thesis II", theoryHours: null, theoryCredit: null, sessionalHours: 6.00, sessionalCredit: 3.00, totalCredit: 3.00 }
                ],
                total: { theoryHours: 12.0, theoryCredit: 12.0, sessionalHours: 6.0, sessionalCredit: 3.00, totalCredit: 15.00 }
            }
        ]
    }
    
];

export default curriculumData;