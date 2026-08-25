import Link from 'next/link';
import React from 'react';
import avatarImg from '../../../../../public/assets/images/avatar/avatar.webp';
import avatarTwoImg from '../../../../../public/assets/images/avatar/avatar3.webp';
import avatarThreeImg from '../../../../../public/assets/images/avatar/avatar3.webp';
import avatarFourImg from '../../../../../public/assets/images/avatar/avatar2.webp';
import Image, { StaticImageData } from 'next/image';

interface CommentProps {
    avatar: StaticImageData;
    name: string;
    date: string;
    text: string;
    replies?: CommentProps[];
}

const Comment: React.FC<CommentProps> = ({ avatar, name, date, text, replies = [] }) => (
    <li>
        <div className="bd-postbox-comment-box">
            <div className="bd-postbox-comment-info">
                <div className="bd-postbox-comment-avatar">
                    <Image src={avatar} alt="avatar" />
                </div>
            </div>
            <div className="bd-postbox-comment-text">
                <div className="bd-postbox-comment-name">
                    <h5 className="title mb--5"><Link href="#">{name}</Link></h5>
                    <span className="post-meta">{date}</span>
                </div>
                <p>{text}</p>
                <div className="bd-postbox-comment-reply">
                    <Link href="#">Reply</Link>
                </div>
            </div>
        </div>
        {replies.length > 0 && (
            <ul className="children">
                {replies.map((reply, index) => (
                    <Comment key={index} {...reply} />
                ))}
            </ul>
        )}
    </li>
);

const PostboxComment: React.FC = () => {
    const comments: CommentProps[] = [
        {
            avatar: avatarImg,
            name: "James Smith",
            date: "July 14, 2024",
            text: "This course exceeded my expectations! The instructor's knowledge and delivery were excellent. Highly recommend for anyone interested in data science.",
            replies: [
                {
                    avatar: avatarTwoImg,
                    name: "Michael Brown",
                    date: "July 14, 2024",
                    text: "The practical approach of this course is what made it so engaging. The instructor's real-world examples were spot on!"
                }
            ]
        },
        {
            avatar: avatarThreeImg,
            name: "Michael Brown",
            date: "July 14, 2024",
            text: "Great course for beginners and experts alike! The instructor breaks down complex topics very well."
        },
        {
            avatar: avatarFourImg,
            name: "Emily Johnson",
            date: "July 14, 2024",
            text: "Excellent structure and well-paced! The instructor has a great teaching style that makes learning fun."
        }
    ];

    return (
        <div className="bd-postbox-comment">
            <ul>
                {comments.map((comment, index) => (
                    <Comment key={index} {...comment} />
                ))}
            </ul>
        </div>
    );
};

export default PostboxComment;
