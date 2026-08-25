import { ICourseReview } from "@/interFace/interFace";
import avatarImg from '../../../public/assets/images/avatar/avatar.webp';
import avatarImg2 from '../../../public/assets/images/avatar/avatar2.webp';
import avatarImg3 from '../../../public/assets/images/avatar/avatar3.webp';

const courseReviews: ICourseReview[] = [
    {
        id: 1,
        name: 'James Smith',
        date: 'July 14, 2024',
        avatar: avatarImg,
        rating: 5,
        comment: "This course exceeded my expectations! The instructor's knowledge and delivery were excellent. Highly recommend for anyone interested in data science.",
        replies: [
            {
                id: 2,
                name: 'Michael Brown',
                date: 'July 14, 2024',
                avatar: avatarImg3,
                rating: 0,
                comment: "The practical approach of this course is what made it so engaging. The instructor's real-world examples were spot on!"
            }
        ]
    },
    {
        id: 3,
        name: 'Michael Brown',
        date: 'July 14, 2024',
        avatar: avatarImg3,
        rating: 5,
        comment: "Great course for beginners and experts alike! The instructor breaks down complex topics very well."
    },
    {
        id: 4,
        name: 'Emily Johnson',
        date: 'July 14, 2024',
        avatar: avatarImg2,
        rating: 5,
        comment: "Excellent structure and well-paced! The instructor has a great teaching style that makes learning fun."
    },
    //course details two review data
    {
        id: 5,
        name: 'James Smith',
        avatar: avatarImg,
        date: 'July 14, 2024',
        rating: 5,
        comment: `This course exceeded my expectations! The instructor's knowledge and delivery were excellent. Highly recommend for anyone interested in data science.`,
    },
    {
        id: 6,
        name: 'Michael Brown',
        avatar: avatarImg3,
        date: 'July 14, 2024',
        rating: 5,
        comment: `Great course for beginners and experts alike! The instructor breaks down complex topics very well.`,
    },
    {
        id: 7,
        name: 'Emily Johnson',
        avatar: avatarImg2,
        date: 'July 14, 2024',
        rating: 5,
        comment: `Excellent structure and well-paced! The instructor has a great teaching style that makes learning fun.`,
    }, 
];

export default courseReviews;