import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import LogoWhite from '../../../public/assets/images/logo/logo-white.svg';
import FooterTopBg from '../../../public/assets/images/bg/footer-top-bg-2.webp';
import FooterRightShape from '../../../public/assets/images/shape/footer-right-shape.webp';
import FooterLeftShape from '../../../public/assets/images/shape/footer-left-shape.webp';
import { getCurrentYear } from '@/utils/dateUtils';

const footerSections = [
    {
        title: "Links",
        links: [
            { name: "About Us", href: "/about-online-course" },
            { name: "News", href: "/blog" },
            { name: "Event", href: "/event" },
            { name: "Shop", href: "/shop" },
            { name: "Pricing Table", href: "/pricing-table" },
        ]
    },
    {
        title: "Class",
        links: [
            { name: "Courses", href: "/courses" },
            { name: "Instructor", href: "/instructor" },
            { name: "Instructor Details", href: "/instructor/instructor-details" },
            { name: "Become Instructor", href: "/become-instructor" },
            { name: "Student Panel", href: "/student-dashboard" },
        ]
    },
    {
        title: "Support",
        links: [
            { name: "Contact Now", href: "/contact" },
            { name: "FAQ", href: "/faq" },
            { name: "Purchase Guide", href: "/purchase-guide" },
            { name: "Terms Conditions", href: "/terms-conditions" },
            { name: "Privacy Policy", href: "/privacy-policy" },
        ]
    }
];

const QuranLearningFooter = () => {
    return (
        <>
            {/* -- footer area start -- */}
            <footer className="bd-footer p-relative z-3">
                <div className="bd-footer-top-shape"><Image src={FooterTopBg} alt="image" /></div>
                <div className="bd-footer-shape">
                    <div className="shape-1"><Image src={FooterRightShape} style={{ width: "100%", height: "auto" }} alt="shape" /></div>
                    <div className="shape-2"><Image src={FooterLeftShape} alt="shape" /></div>
                </div>
                <div className="bd-footer-area style-two section-space-bottom theme-black fix">
                    <div className="container">
                        <div className="row gy-30 justify-content-between">
                            <div className="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-12">
                                <div className="bd-footer-widget footer-2-col-1">
                                    <div className="bd-footer-widget-logo">
                                        <Link href="/">
                                            <Image src={LogoWhite} alt="image" priority />
                                        </Link>
                                    </div>
                                    <div className="bd-footer-widget-content">
                                        <p className="bd-footer-widget-description">
                                            Education focused website or template is an essential part that provides visitors with
                                            insights into the program or service offered.
                                        </p>
                                        <div className="bd-footer-social">
                                            <div className="theme-social has-white">
                                                <ul className="social-icon-list">
                                                    <li><Link href="https://www.facebook.com/" target="_blank"><i className="fa-brands fa-facebook-f"></i></Link>
                                                    </li>
                                                    <li><Link href="https://x.com/" target="_blank"><i className="fa-brands fa-x-twitter"></i></Link>
                                                    </li>
                                                    <li><Link href="https://www.linkedin.com/feed/" target="_blank"><i className="fa-brands fa-linkedin-in"></i></Link>
                                                    </li>
                                                    <li><Link href="https://www.instagram.com/" target="_blank"><i className="fa-brands fa-instagram"></i></Link>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Dynamic footer sections */}
                            {footerSections.map((section, index) => (
                                <div key={index} className="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-sm-4">
                                    <div className={`bd-footer-widget footer-2-col-${index + 2}`}>
                                        <h6 className="bd-footer-widget-title">{section.title}</h6>
                                        <div className="bd-footer-widget-links">
                                            <ul>
                                                {section.links.map((link, idx) => (
                                                    <li key={idx} className="underline-two">
                                                        <Link href={link.href}>{link.name}</Link>
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            ))}

                            <div className="col-xxl-3 col-xl-3 col-lg-3 col-md-8 col-sm-12">
                                <div className="bd-footer-widget footer-2-col-5">
                                    <h6 className="bd-footer-widget-title">Newsletter</h6>
                                    <div className="bd-footer-from-content">
                                        <div className="bd-footer-widget-subscribe">
                                            <p className="bd-footer-widget-description">
                                                Subscribe our newsletter to get the latest news & updates.
                                            </p>
                                            <form action="#">
                                                <div className="bd-footer-subscribe-form style-two place-text-white">
                                                    <input type="email" placeholder="Email address" />
                                                    <button className="bd-btn btn-primary h-40px" type="submit">Subscribe</button>
                                                </div>
                                            </form>
                                            <div className="checkout-agree">
                                                <div className="checkout-option">
                                                    <input id="read_all" type="checkbox" />
                                                    <label htmlFor="read_all">I have read and agree to the website.</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="bd-footer-copyright-area style-two full-black-bg fix bd-noise-bg">
                    <div className="container">
                        <div className="row justify-content-between">
                            <div className="col-xl-12">
                                <div className="bd-footer-copyright-text text-center">
                                    <p className="underline-two">© Copyright <span>{getCurrentYear()}</span> | Developed By iStudy</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            {/* -- footer area end -- */}
        </>
    );
};

export default QuranLearningFooter;
