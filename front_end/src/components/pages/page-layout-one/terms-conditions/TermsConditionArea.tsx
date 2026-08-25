import Link from 'next/link';
import React from 'react';

const TermsConditionArea = () => {
    return (
        <>
            <section className="bd-policy-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-10">
                            <div className="bd-policy-wrapper p-relative z-index-1">
                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Introduction</h4>
                                    <p>Welcome to iStudy. These Terms and Conditions govern your use of our platform. By accessing
                                        or using our website, you agree to be bound by these Terms and Conditions and our Privacy
                                        Policy. If you do not agree to these Terms and Conditions, please do not use our platform.
                                    </p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Use of Our Platform</h4>
                                    <p className="mb-10">You may use our platform for educational purposes only. You agree not to use
                                        our
                                        platform:</p>
                                    <ul>
                                        <li>In any way that violates any applicable law or regulation.</li>
                                        <li>To engage in any conduct that restricts or inhibits {`anyone's`} use or enjoyment of the
                                            platform.</li>
                                    </ul>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Intellectual Property</h4>
                                    <p>All content on our platform, including but not limited to text, graphics, logos, images, and
                                        software, is the property of iStudy or its content suppliers and is protected by
                                        copyright laws.</p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Links to Third-Party Websites</h4>
                                    <p>Our platform may contain links to third-party websites that are not owned or controlled by
                                        us.
                                        We have no control over, and assume no responsibility for, the content, privacy policies, or
                                        practices of any third-party websites. You acknowledge and agree that we shall not be
                                        responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be
                                        caused by or in connection with the use of or reliance on any such content, goods, or
                                        services available on or through any such third-party websites.</p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Limitation of Liability</h4>
                                    <p>In no event shall iStudy, nor its directors, employees, partners, agents, suppliers, or
                                        affiliates, be liable for any indirect, incidental, special, consequential, or punitive
                                        damages, including without limitation, loss of profits, data, use, goodwill, or other
                                        intangible losses, resulting from (i) your access to or use of or inability to access or use
                                        our platform; (ii) any conduct or content of any third party on our platform; (iii) any
                                        content
                                        obtained from our platform; and (iv) unauthorized access, use, or alteration of your
                                        transmissions or content, whether based on warranty, contract, tort (including negligence),
                                        or any other legal theory, whether or not we have been informed of the possibility of such
                                        damage, and even if a remedy set forth herein is found to have failed of its essential
                                        purpose.</p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Governing Law</h4>
                                    <p>These Terms and Conditions shall be governed by and construed in accordance with the laws of
                                        [YourCountry], without regard to its conflict of law provisions.</p>
                                </div>

                                <div className="bd-policy-item">
                                    <h4 className="bd-policy-title">Changes to Terms and Conditions</h4>
                                    <p>We reserve the right to modify these Terms and Conditions at any time. If we make material
                                        changes to these Terms and Conditions, we will notify you by posting the updated Terms and
                                        Conditions on our platform.</p>
                                </div>

                                <div className="bd-policy-contact">
                                    <h4 className="bd-policy-title">Contact Us</h4>
                                    <p className="mb-10">If you have any questions or concerns about these Terms and Conditions, please
                                        contact us at</p>

                                    <ul>
                                        <li>Email: <span><Link href="mailto:contact@istudy.com">contact@istudy.com</Link></span></li>
                                    </ul>

                                    <div className="bd-policy-address">
                                        <p className="mb-0">
                                            <Link href="#">1234 Education Lane, <br/> Knowledge City, 56789, United State</Link>
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

export default TermsConditionArea;