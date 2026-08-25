import Link from 'next/link';
import React from 'react';
import avatar1 from '../../../../../../public/assets/images/avatar/avatar.webp';
import avatar2 from '../../../../../../public/assets/images/avatar/avatar3.webp';
import avatar3 from '../../../../../../public/assets/images/avatar/avatar2.webp';
import avatar4 from '../../../../../../public/assets/images/avatar/avatar4.webp';
import Image, { StaticImageData } from 'next/image';

// Define types for the props of CommentItem
interface Reply {
  avatar: StaticImageData;
  name: string;
  date: string;
  text: string;
  replies?: Reply[];
}

interface CommentItemProps {
  avatar: StaticImageData;
  name: string;
  date: string;
  text: string;
  replies?: Reply[];
}

const CommentItem: React.FC<CommentItemProps> = ({ avatar, name, date, text, replies = [] }) => {
  return (
    <li>
      <div className="bd-postbox-comment-box d-sm-flex align-items-start">
        <div className="bd-postbox-comment-info">
          <div className="bd-postbox-comment-avatar">
            <Image src={avatar} alt={name} />
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
            <CommentItem key={index} {...reply} />
          ))}
        </ul>
      )}
    </li>
  );
};

const PostboxComment: React.FC = () => {
  const comments: CommentItemProps[] = [
    {
      avatar: avatar1,
      name: "Jessica Taylor",
      date: "Aug 15, 2024",
      text: "The Deluxe Building Blocks Set is a fantastic product! My kids love creating new designs every day. The quality of the blocks is excellent and safe for little hands!",
      replies: [
        {
          avatar: avatar2,
          name: "Liam Johnson",
          date: "Aug 16, 2024",
          text: "This set has provided endless entertainment. The blocks fit together well, and the vibrant colors are a big hit!",
        }
      ]
    },
    {
      avatar: avatar3,
      name: "Sophia Brown",
      date: "Aug 17, 2024",
      text: "Great educational toy! It encourages creativity and problem-solving in my children. Highly recommend!"
    },
    {
      avatar: avatar4,
      name: "Mia Wilson",
      date: "Aug 18, 2024",
      text: "While the set is fun, I wish it came with more pieces. My kids go through them quickly when they build larger structures."
    }
  ];

  return (
    <ul>
      {comments.map((comment, index) => (
        <CommentItem key={index} {...comment} />
      ))}
    </ul>
  );
};

export default PostboxComment;
