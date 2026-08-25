import { ProgramDataType } from "@/interFace/interFace";

//course thumb image
import courseThumbOne from '../../public/assets/images/course/course-thumb-1.webp';
import courseThumbTwo from '../../public/assets/images/course/course-thumb-2.webp';
import courseThumbThree from '../../public/assets/images/course/course-thumb-3.webp';
import courseThumbFour from '../../public/assets/images/course/course-thumb-4.webp';
import courseThumbFive from '../../public/assets/images/course/course-thumb-5.webp';
import courseThumbSix from '../../public/assets/images/course/course-thumb-6.webp';

//course shape
import courseShapeOne from '../../public/assets/images/course/course-shape-1.webp';
import courseShapeTwo from '../../public/assets/images/course/course-shape-2.webp';
import courseShapeThree from '../../public/assets/images/course/course-shape-3.webp';
import courseShapeFour from '../../public/assets/images/course/course-shape-4.webp';
import courseShapeFive from '../../public/assets/images/course/course-shape-5.webp';
import courseShapeSix from '../../public/assets/images/course/course-shape-6.webp';

// text shape
import cscTextShape from '../../public/assets/images/course/csc.webp';
import bapTextShape from '../../public/assets/images/course/bap.webp';
import bbaTextShape from '../../public/assets/images/course/bba.webp';
import mscdsTextShape from '../../public/assets/images/course/mscds.webp';
import mphTextShape from '../../public/assets/images/course/mph.webp';
import aiTextShape from '../../public/assets/images/course/ai.webp';
//kindergraten image
import homeProgram1 from "../../public/assets/images/program/home-program-1.webp";
import homeProgram2 from "../../public/assets/images/program/home-program-2.webp";
import homeProgram3 from "../../public/assets/images/program/home-program-3.webp";
import programShape from "../../public/assets/images/shape/course-shape.webp";


export const programData: ProgramDataType[] = [
    {
        id: 1,
        program: 'undergraduate',
        type: "FULL-TIME",
        title: "BSc in Computer Science",
        description: "Learn programming, algorithms, and computational theory to drive the future of technology.",
        duration: "4 Years",
        credits: "12 Credits",
        image: courseThumbOne,
        shapeImage: courseShapeOne,
        textImage: cscTextShape,
    },
    {
        id: 2,
        program: 'undergraduate',
        type: "PART-TIME",
        title: "Bachelor of Arts in Psychology",
        description: "Understand human behavior and mental processes through a comprehensive study of psychology.",
        duration: "3 Years",
        credits: "10 Credits",
        image: courseThumbTwo,
        shapeImage: courseShapeTwo,
        textImage: bapTextShape,
    },
    {
        id: 3,
        program: 'undergraduate',
        type: "ONLINE",
        title: "Bachelor of Business Administration",
        description: "Equip yourself with advanced business strategies and management skills for a dynamic market.",
        duration: "2 Years",
        credits: "15 Credits",
        image: courseThumbThree,
        shapeImage: courseShapeThree,
        textImage: bbaTextShape,
    },
    {
        id: 4,
        program: 'graduate',
        type: "FULL-TIME",
        title: "Master of Science in Data Science",
        description: "Master statistical methods, machine learning, and big data analysis to uncover insights in data-driven fields.",
        duration: "2 Years",
        credits: "16 Credits",
        image: courseThumbFour,
        shapeImage: courseShapeFour,
        textImage: mscdsTextShape,
    },
    {
        id: 5,
        program: 'graduate',
        type: "PART-TIME",
        title: "Master of Public Health (MPH)",
        description: "Develop skills to address global health challenges and improve community well-being through public health strategies.",
        duration: "2 Years",
        credits: "14 Credits",
        image: courseThumbFive,
        shapeImage: courseShapeFive,
        textImage: mphTextShape,
    },
    {
        id: 6,
        program: 'phd',
        type: "RESEARCH",
        title: "PhD in Artificial Intelligence",
        description: "Conduct groundbreaking research in AI, machine learning, and deep learning to push the boundaries of technology and innovation.",
        duration: "5 Years",
        credits: "20 Credits",
        image: courseThumbSix,
        shapeImage: courseShapeSix,
        textImage: aiTextShape,
    },
    //kindergraten programs
    {
        id: 7,
        image: homeProgram1,
        shapeImage: programShape,
        BgClass: "theme-bg",
        title: "Settling",
        hoursTime: 50,
        age: "4-5 Yrs",
        duration: "5 Days",
        description: "To round out our weekend of celebrations, we are holding our reunion.",
    },
    {
        id: 8,
        image: homeProgram2,
        shapeImage: programShape,
        BgClass: "warning-bg",
        title: "Play Group",
        hoursTime: 50,
        age: "4-5 Yrs",
        duration: "5 Days",
        description: "Help children develop social skills and emotional understanding through interactive group sessions.",
    },
    {
        id: 9,
        image: homeProgram3,
        shapeImage: programShape,
        BgClass: "info-bg",
        title: "Pre-Nursery",
        hoursTime: 50,
        age: "4-5 Yrs",
        duration: "5 Days",
        description: "Nurture creativity with hands-on art and craft projects, perfect for little imaginations.",
    },
    {
        id: 10,
        image: homeProgram1,
        shapeImage: programShape,
        BgClass: "theme-bg",
        title: "Settling",
        hoursTime: 50,
        age: "4-5 Yrs",
        duration: "5 Days",
        description: "To round out our weekend of celebrations, we are holding our reunion.",
    },
    {
        id: 11,
        image: homeProgram2,
        shapeImage: programShape,
        BgClass: "warning-bg",
        title: "Pre-Nursery",
        hoursTime: 50,
        age: "4-5 Yrs",
        duration: "5 Days",
        description: "Help children develop social skills and emotional understanding through interactive group sessions.",
    },
];

export default programData;

