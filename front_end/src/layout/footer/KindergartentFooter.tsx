import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import logoWhite from '../../../public/assets/images/logo/logo-white.svg';

interface ISubFooter {
    href: string,
    label: string
}
interface FooterWidgetProps {
    title: string;
    links: ISubFooter[];
}

const FooterWidget: React.FC<FooterWidgetProps> = ({ title, links }) => (
    <div className="bd-footer-widget">
        <h6 className="bd-footer-widget-title has-color-off">{title}</h6>
        <div className="bd-footer-widget-links has-white">
            <ul>
                {links.map((link, index) => (
                    <li key={index} className="underline-two">
                        <Link href={link.href}>{link.label}</Link>
                    </li>
                ))}
            </ul>
        </div>
    </div>
);

const ContactInfo = () => (
    <div className="bd-footer-widget">
        <h6 className="bd-footer-widget-title has-color-off">Contact Us</h6>
        <div className="bd-footer-widget-content">
            <div className="bd-footer-widget-contact-info mb-25">
                <div className="bd-footer-widget-contact-item has-white">
                    <span>Address:</span>
                    <Link href="#"> 27 Division St, New York</Link>
                </div>
                <div className="bd-footer-widget-contact-item has-white">
                    <span>Phone:</span>
                    <Link href="tel:123456789">+123-4567-8900</Link>
                </div>
                <div className="bd-footer-widget-contact-item has-white">
                    <span>Email:</span>
                    <Link href="mailto:istudy@mail.com"> istudy@mail.com</Link>
                </div>
            </div>
            <div className="bd-footer-social">
                <div className='theme-social secondary'>
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
    </div>
);

const KindergartenFooter = () => {
    const navigationLinks = [
        { href: "/about-kindergarten", label: "About Us" },
        { href: "/courses", label: "Courses" },
        { href: "/blog", label: "News" },
        { href: "/shop", label: "Shop" },
        { href: "/contact-us", label: "Contact" }
    ];

    const quickLinks = [
        { href: "/mvs", label: "Vision, Mission & Strategy" },
        { href: "/event", label: "Event" },
        { href: "/purchase-guide", label: "Purchase Guide" },
        { href: "/webinar-details", label: "Webinar Details" },
        { href: "/pricing-table", label: "Pricing Table" }
    ];

    const programs = [
        { href: "/kindergarten-program-details", label: "Play School" },
        { href: "/kindergarten-program-details", label: "Nursery" },
        { href: "/kindergarten-program-details", label: "Senior Kg" },
        { href: "/kindergarten-program-details", label: "Junior Kg" },
        { href: "/kindergarten-program-details", label: "Day Care" }
    ];

    return (
        <footer>
            <div className="bd-footer-wave p-relative fix theme-bg">
                <div className="bd-wave-wrapper">
                    <div className="bd-wave"></div>
                    <div className="bd-wave"></div>
                </div>
                <div className="container">
                    <div className="bd-footer-top section-space-small">
                        <div className="row gy-30 justify-content-between">
                            <div className="col-lg-6">
                                <div className="bd-footer-top-widget">
                                    <div className="bd-footer-logo mb-15">
                                        <Link href="index">
                                            <Image src={logoWhite} alt="logo" />
                                        </Link>
                                    </div>
                                    <p className="bd-footer-widget-description light-color">
                                        Education focused website or template is an essential part that provides visitors with insights into the program.
                                    </p>
                                </div>
                            </div>
                            <div className="col-lg-6">
                                <div className="bd-cta-content">
                                    <div className="bd-section-title-wrapper mb-30">
                                        <h5 className="bd-section-title white-text">Join Our Newsletter</h5>
                                    </div>
                                    <form action="#">
                                        <div className="bd-newsletter-input">
                                            <input type="text" placeholder="Your email" />
                                            <button type="submit" className="bd-btn btn-primary radius-6">Subscribe Now</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="bd-footer-area section-space-small">
                        <div className="row gy-30 justify-content-between">
                            <div className="col-lg-3 col-md-6 col-sm-6">
                                <FooterWidget title="Navigation" links={navigationLinks} />
                            </div>
                            <div className="col-lg-3 col-md-6 col-sm-6">
                                <FooterWidget title="Quick Links" links={quickLinks} />
                            </div>
                            <div className="col-lg-3 col-md-6 col-sm-6">
                                <FooterWidget title="Programs" links={programs} />
                            </div>
                            <div className="col-lg-3 col-md-6 col-sm-6">
                                <ContactInfo />
                            </div>
                        </div>
                    </div>
                </div>
                <div className="bd-footer-copyright-area bd-copyright-bg">
                    <div className="container">
                        <div className="row justify-content-center">
                            <div className="col-xl-8 text-center">
                                <div className='bd-footer-copyright text-center p-relative z-index-11'>
                                    <p className="underline">© Copyright {new Date().getFullYear()} | Developed By iStudy</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
};

export default KindergartenFooter;
