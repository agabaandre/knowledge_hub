import Link from 'next/link';
import React from 'react';
import avatarImg from '../../../../../public/assets/images/avatar/avatar.webp';
import Image from 'next/image';

const PostboxAuthor = () => {
    return (
        <>
            <div className="bd-postbox-author mb-30">
                <div className="thumb">
                    <Link href="#"><Image src={avatarImg} alt="Author image" /></Link>
                </div>
                <div className="bd-postbox-author-info">
                    <div className="mb--5">
                        <h6 className="name underline"><Link href="#">Sarah Collins</Link></h6>
                        <span className="designation">Senior Education Consultant</span>
                    </div>
                    <div className="bd-postbox-author-bio mb-15">
                        <p>Sarah is an EdTech Consultant with over 8 years of experience in integrating
                            technology into education to enhance learning experiences.</p>
                    </div>
                    <div className="theme-social circle">
                        <ul className="social-icon-list">
                            <li><Link href="https://www.facebook.com/" target="_blank"><i className="fa-brands fa-facebook-f"></i></Link></li>
                            <li><Link href="https://x.com/" target="_blank"><i className="fa-brands fa-x-twitter"></i></Link></li>
                            <li><Link href="https://www.linkedin.com/feed/" target="_blank"><i className="fa-brands fa-linkedin-in"></i></Link></li>
                            <li><Link href="https://www.instagram.com/" target="_blank"><i className="fa-brands fa-instagram"></i></Link></li>
                            <li><Link href="https://www.behance.net/" target="_blank"><i className="fa-brands fa-behance"></i></Link></li>
                        </ul>
                    </div>
                </div>
            </div>
        </>
    );
};

export default PostboxAuthor;