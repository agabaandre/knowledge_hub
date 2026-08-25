
import Image, { StaticImageData } from "next/image";
import Link from "next/link";
import React from "react";
import AvatarImg from "../../../../../../public/assets/images/avatar/avatar.webp";
import avatarImgThree from "../../../../../../public/assets/images/avatar/avatar3.webp";
import avatarImgTwo from "../../../../../../public/assets/images/avatar/avatar2.webp";

interface ReviewProps {
    avatar: StaticImageData;
    name: string;
    date: string;
    review: string;
}

const ReviewItem: React.FC<ReviewProps> = ({ avatar, name, date, review }) => {
    return (
        <li>
            <div className="bd-postbox-comment-box d-sm-flex align-items-start">
                <div className="bd-postbox-comment-info">
                    <div className="bd-postbox-comment-avatar thumb-radius">
                        <Image src={avatar} alt={name} />
                    </div>
                </div>
                <div className="bd-postbox-comment-text">
                    <div className="d-flex flex-wrap gap-15 align-items-center justify-content-between mb-10">
                        <div className="post-rating rating-spacing-2">
                            {Array(5)
                                .fill(null)
                                .map((_, index) => (
                                    <Link href="#" key={index}>
                                        <i className="fa fa-star"></i>
                                    </Link>
                                ))}
                        </div>
                        <button className="bd-like-btn" type="button">
                            <i className="fa-regular fa-heart"></i>
                        </button>
                    </div>
                    <div className="bd-postbox-comment-name">
                        <h5 className="title mb-5">
                            <Link href="#">{name}</Link>
                        </h5>
                        <span className="post-meta">{date}</span>
                    </div>
                    <p>{review}</p>
                </div>
            </div>
        </li>
    );
};

const BookReview: React.FC = () => {
    const reviews = [
        {
            avatar: AvatarImg,
            name: "Sophia Turner",
            date: "September 25, 2024",
            review: "A fantastic read! The twists kept me guessing until the last page. Highly recommend!",
        },
        {
            avatar: avatarImgThree,
            name: "Liam Davis",
            date: "September 26, 2024",
            review: "Intriguing characters and a gripping plot! I couldn't put it down. Would read again!",
        },
        {
            avatar: avatarImgTwo,
            name: "Olivia White",
            date: "September 27, 2024",
            review: "Great blend of humor and mystery! The pacing was perfect, and I loved the ending.",
        },
    ];

    return (
        <ul>
            {reviews.map((review, index) => (
                <ReviewItem key={index} {...review} />
            ))}
        </ul>
    );
};

export default BookReview;
