import { StaticImageData } from "next/image";
//blog style 01 image
import blogImg1 from "../../../public/assets/images/blog/blog-thumb-16.webp";
import blogImg2 from "../../../public/assets/images/blog/blog-thumb-05.webp";
import blogImg3 from "../../../public/assets/images/blog/blog-thumb-06.webp";
//blog style 02 image
import blogImg4 from "../../../public/assets/images/blog/blog-thumb-10.webp";
import blogImg5 from "../../../public/assets/images/blog/blog-thumb-07.webp";
import blogImg6 from "../../../public/assets/images/blog/blog-thumb-08.webp";
//blog style 03 image
import blogImg7 from "../../../public/assets/images/blog/blog-thumb-18.webp";
//blog style 08 image
import blogImg8 from "../../../public/assets/images/blog/blog-thumb-19.webp";
import blogImg9 from "../../../public/assets/images/blog/blog-thumb-20.webp";
import blogImg10 from "../../../public/assets/images/blog/blog-thumb-21.webp";
//blog style 11 image
import blogImg11 from "../../../public/assets/images/blog/blog-thumb-11.webp";


interface BlogData {
    id: number;
    image: StaticImageData;
    title: string;
    authorName?: string;
    date: string;
    description?: string;
    category?: string;
    badge?: string
    badgeColor?: string
    comment?: number;
}

const elementBlogData: BlogData[] = [
    //element blog style 01 data start
    {
        id: 1,
        image: blogImg1,
        title: "The Impact of Technology in Modern Education",
        authorName: "John Smith",
        date: "13 Aug 2024",
    },
    {
        id: 2,
        image: blogImg2,
        title: "Top Strategies for Effective Online Learning",
        authorName: "Sophia Adams",
        date: "13 Aug 2024",
    },
    {
        id: 3,
        image: blogImg3,
        title: "How to Build a Successful Academic Career",
        authorName: "Emma Johnson",
        date: "13 Aug 2024",
    },
    //element blog style 01 data end
    //element blog style 02 data start
    {
        id: 4,
        image: blogImg4,
        title: "Effective Study Techniques for Every Student",
        date: "July 24, 2024",
        category: "Education, Tips",
        description: "Explore proven methods to boost your learning efficiency, from active recall to strategic breaks.",
    },
    {
        id: 5,
        image: blogImg5,
        title: "Top Strategies for Effective Online Learning",
        date: "July 24, 2024",
        category: "Online Learning",
        description: "Master the art of virtual learning with tips on time management, focus, and effective participation.",
    },
    {
        id: 6,
        image: blogImg6,
        title: "Top Skills for the Future Job Market",
        authorName: "Emma Johnson",
        date: "July 24, 2024",
        category: "Career Development",
        description: "Stay ahead by developing in-demand skills like coding, critical thinking, and emotional intelligence.",
    },
    //element blog style 02 data end
    //element blog style 03 data start
    {
        id: 7,
        badge: "Education",
        image: blogImg1,
        title: "Adapting to the Future of Online Education",
        date: "Mar 12, 2024",
        category: "Education, Tips",
        comment: 15,
        description: "Discover how online platforms are transforming the education landscape and shaping the future of learning.",
    },
    {
        id: 8,
        badge: "Technology",
        image: blogImg7,
        title: "The Role of AI in Personalized Learning",
        date: "Mar 12, 2024",
        comment: 20,
        category: "Online Learning",
        description: "Explore how artificial intelligence is revolutionizing education by tailoring learning experiences to individual needs.",
    },
    //element blog style 04 data start
    {
        id: 9,
        image: blogImg1,
        title: "Effective Study Techniques for Students",
        date: "Feb 25, 2024",
        authorName: "Jaminson",
        description: "Explore proven strategies to enhance focus, retain knowledge, and excel academically.",
    },
    {
        id: 10,
        image: blogImg2,
        title: "Top Resources for Online Learning",
        date: "Feb 25, 2024",
        authorName: "Peterson",
        description: "Discover the best tools and platforms to make the most out of your online education journey.",
    },
    {
        id: 11,
        image: blogImg3,
        title: "How to Balance Study and Work",
        authorName: "Maxwell",
        date: "Feb 25, 2024",
        description: "Learn practical tips for juggling academics and professional life successfully.",
    },
    //element blog style 05 data start
    {
        id: 12,
        badge: "Tech Trends",
        badgeColor: "badge-success",
        image: blogImg1,
        title: "The Future of Artificial Intelligence",
        date: "Aug 10 2024",
        authorName: "Alice Johnson",
    },
    {
        id: 13,
        badge: "Web Development",
        badgeColor: "badge-warning",
        image: blogImg2,
        title: "Mastering Responsive Design: Tips and Tricks",
        date: " Aug 15 2024",
        authorName: "Michael Smith",
    },
    {
        id: 14,
        badge: "Data Science",
        badgeColor: "badge-info",
        image: blogImg3,
        title: "Unlocking the Secrets of Machine Learning",
        date: "Aug 20 2024",
        authorName: "Emma Brown",
    },
    //element blog style 06 data start
    {
        id: 15,
        image: blogImg1,
        title: "Unlocking the Power of Online Learning in Modern Education",
        date: "Jan 25 2024",
        authorName: "Jasson Roy",
        description: "Explore how online learning platforms are transforming education, making knowledge accessible to everyone.",
    },
    {
        id: 16,
        image: blogImg2,
        title: "How to Stay Motivated in Online Courses",
        authorName: "Alex Hales",
        date: "Jan 25 2024",
        description: "Discover practical strategies to stay engaged and achieve success in your online learning journey.",
    },
    //element blog style 07 data start
    {
        id: 17,
        badge: "Education",
        badgeColor: "badge-primary",
        image: blogImg1,
        title: "5 Ways to Enhance Online Learning Experiences",
        date: "Jan 25 2024",
        authorName: "Emily Johnson",
    },
    {
        id: 18,
        badge: "Education",
        badgeColor: "badge-warning",
        image: blogImg2,
        title: "Top Strategies for Effective Classroom Management",
        date: "Jan 25 2024",
        authorName: "Sarah Parker",
    },
    {
        id: 19,
        badge: "Education",
        badgeColor: "badge-danger",
        image: blogImg3,
        title: "How Technology is Revolutionizing Education",
        date: "Jan 25 2024",
        authorName: "James Smith",
    },
    //element blog style 08 data start
    {
        id: 20,
        image: blogImg8,
        title: "How to Enhance Your Child’s Learning at Home",
        date: "Feb 25 2024",
        authorName: "Emily Johnson",
    },
    {
        id: 21,
        image: blogImg9,
        title: "Effective Study Techniques for Your Child’s Success",
        date: "Feb 25 2024",
        authorName: "Sarah Parker",
    },
    {
        id: 22,
        image: blogImg10,
        title: "The Role of Parents in Shaping a Child’s Education",
        date: "Feb 25 2024",
        authorName: "James Smith",
    },
    //element blog style 09 data start
    {
        id: 23,
        badge: "iStudy",
        badgeColor: "badge-primary",
        image: blogImg1,
        title: "Effective Home Study Environment for Children",
        date: "Jan 25 2024",
        authorName: "Emily Johnson",
        comment: 5,
    },
    {
        id: 24,
        badge: "iStudy",
        badgeColor: "badge-primary",
        image: blogImg7,
        title: "Top Strategies for Effective Online Learning",
        authorName: "Sarah Parker",
        date: "Jan 25 2024",
        comment: 2,
    },
    //element blog style 10 data start
    {
        id: 25,
        image: blogImg1,
        title: "Creating an Effective Study Space for Your Child",
        authorName: "Emily Johnson",
        date: "13",
        comment:5,
    },
    {
        id: 26,
        image: blogImg2,
        title: "How to Balance Online and Offline Learning",
        date: "13",
        authorName: "David William",
        comment:3,
    },
    {
        id: 27,
        image: blogImg3,
        title: "Effective Parenting Tips for Better Learning",
        date: "13",
        authorName: "Sarah Parker",
        comment:7,
    },
       //element blog style 10 data start
       {
        id: 28,
        image: blogImg8,
        title: "Creating an Effective Study Space for Your Child",
        authorName: "Emily Johnson",
        date: "13 Aug 2024",
        comment:5,
    },
    {
        id: 29,
        image: blogImg4,
        title: "How to Balance Online and Offline Learning",
        date: "13 Aug 2024",
        authorName: "David Warner",
        comment:3,
    },
    {
        id: 30,
        image: blogImg11,
        title: "Effective Parenting Tips for Better Learning",
        date: "13 Aug 2024",
        authorName: "Sarah Parker",
        comment:7,
    },
]

export default elementBlogData;