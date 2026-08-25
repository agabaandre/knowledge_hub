import Link from 'next/link';
import React from 'react';

const PolicyArea = () => {
    return (
        <>
            <section className="bd-policy-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-10">
                            <div className="bd-policy-wrapper p-relative z-index-1">
                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Introduction</h4>
                                    <p>Thank you for visiting iStudy. This Privacy Policy outlines how we collect, use, disclose,
                                        and manage your personal information. By using our platform, you agree to the terms of this
                                        Privacy Policy.</p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Information We Collect</h4>
                                    <p>We collect personal information that you voluntarily provide when you interact with our
                                        platform,
                                        including but not limited to your name, email address, phone number, and academic
                                        information.
                                    </p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">How We Use Your Information</h4>
                                    <p className="mb-10">We may use your personal information to:</p>
                                    <ul>
                                        <li>Provide and enhance our educational services</li>
                                        <li>Communicate with you regarding your courses, inquiries, or updates</li>
                                        <li>Improve the platform’s functionality and offerings</li>
                                        <li>Comply with legal requirements and protect our platform</li>
                                    </ul>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">How We Share Your Information</h4>
                                    <p>We do not sell, trade, or otherwise transfer your personal information to third parties
                                        without your consent, except as required by law or as outlined in this Privacy Policy.</p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Cookies and Tracking Technologies</h4>
                                    <p>We use cookies and similar technologies to enhance user experience, analyze trends, and track
                                        user interactions on our platform.</p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Third-Party Links</h4>
                                    <p>Our platform may include links to third-party websites. We are not responsible for the
                                        content or privacy policies of these external sites.</p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Security</h4>
                                    <p>We implement reasonable security measures to protect your data. However, please note that no
                                        method of data transmission over the internet is 100% secure.</p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Children’s Privacy</h4>
                                    <p>Our platform is not intended for children under the age of 13. We do not knowingly collect
                                        personal information from children under 13. If you believe your child has provided us with
                                        personal information, please contact us immediately.</p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Changes to this Privacy Policy</h4>
                                    <p>We may update this Privacy Policy periodically. Any changes will be posted on this page, and
                                        we encourage you to review it regularly.</p>
                                </div>

                                <div className="bd-policy-contact">
                                    <h4 className="bd-policy-title">Contact Us</h4>
                                    <p className="mb-10">If you have any questions or concerns about our Privacy Policy, please contact
                                        us at:</p>

                                    <ul>
                                        <li>Email: <span><Link href="mailto:contact@istudy.com">contact@istudy.com</Link></span></li>
                                    </ul>

                                    <div className="bd-policy-address">
                                        <p className="mb-0">
                                            <Link href="#">1234 Education Lane, <br /> Knowledge City, 56789, United State</Link>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default PolicyArea;