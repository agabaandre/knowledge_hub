import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import Link from 'next/link';
import React from 'react';

const PurchaseGuideMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Purchase Guide'/>
            {/* -- Terms-area start -- */}
        <section className="bd-policy-area section-space">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-xl-10 col-md-12">
                        <div className="bd-policy-wrapper p-relative z-index-1">
                            <div className="bd-policy-item">
                                <h4 className="bd-policy-title">iStudy Purchase Guide</h4>
                                <p>Welcome to iStudy, your ultimate destination for quality online learning! Follow this
                                    step-by-step guide to purchase your desired courses:</p>
                            </div>
                            <div className="bd-policy-item">
                                <h4 className="bd-policy-title">Step 1: Browse Courses</h4>
                                <ul>
                                    <li>Visit the iStudy website and navigate to the Courses section.</li>
                                    <li>Use filters to sort courses by category, skill level, price, or duration.</li>
                                    <li>Click on a course to view its details, including syllabus, instructor information, and
                                        reviews.</li>
                                </ul>
                            </div>
                            <div className="bd-policy-item">
                                <h4 className="bd-policy-title">Step 2: Add to Cart</h4>
                                <ul>
                                    <li>Once {`you've`} found a course you want, click the {`"Enroll Now" or "Add to Cart"`} button.</li>
                                    <li>Continue browsing or proceed to checkout.</li>
                                </ul>
                            </div>
                            <div className="bd-policy-item">
                                <h4 className="bd-policy-title">Step 3: Create an Account</h4>
                                <ul>
                                    <li>If you’re a new user, sign up with your email or social account.</li>
                                    <li>Existing users can log in using their credentials.</li>
                                </ul>
                            </div>
                            <div className="bd-policy-item">
                                <h4 className="bd-policy-title">Step 4: Review Your Cart</h4>
                                <ul>
                                    <li>Check the courses in your cart for accuracy.</li>
                                    <li>Apply any available promo codes or discounts.</li>
                                </ul>
                            </div>
                            <div className="bd-policy-item">
                                <h4 className="bd-policy-title">Step 5: Make Payment</h4>
                                <ul>
                                    <li>Choose a payment method: Credit/Debit Card, PayPal, or other supported options.</li>
                                    <li>Enter your payment details and confirm the transaction.</li>
                                </ul>
                            </div>
                            <div className="bd-policy-item">
                                <h4 className="bd-policy-title">Step 6: Access Your Course</h4>
                                <ul>
                                    <li>After successful payment, go to your Dashboard.</li>
                                    <li>Click on the purchased course to begin learning.</li>
                                    <li>Enjoy lifetime access (if applicable) or access for the specified duration.</li>
                                </ul>
                            </div>

                            <div className="bd-policy-contact">
                                <h4 className="bd-policy-title">Need Help?</h4>
                                <p className="mb-10">For any queries, visit our Help Center or contact our Customer Support Team
                                    via:</p>

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
        {/* -- Terms-area end -- */}
        </>
    );
};

export default PurchaseGuideMain;