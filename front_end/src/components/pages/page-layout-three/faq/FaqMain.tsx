import AboutCtaArea from '@/components/common/about-cta/AboutCtaArea';
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import Image from 'next/image';
import React from 'react';
import faqShape from '../../../../../public/assets/images/faq/faq-shape.png';

const FaqMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Frequently Asked Questions' />
            {/* -- faq area start -- */}
            <section className="bd-faq-area section-space-top p-relative">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xl-4 col-lg-5">
                            <div className="bd-section-title-wrapper section-title-space">
                                <h2 className="bd-section-title">Need Help? <br className="d-none d-lg-block" /> Find Answers Here</h2>
                            </div>
                            <div className="bd-faq-tab-menu">
                                <ul className="nav nav-pills" id="pills-tab" role="tablist">
                                    <li className="nav-item" role="presentation">
                                        <button className="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">General Questions</button>
                                    </li>
                                    <li className="nav-item" role="presentation">
                                        <button className="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Advanced questions</button>
                                    </li>
                                    <li className="nav-item" role="presentation">
                                        <button className="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Purchases & Refunds</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div className="col-xl-8 col-lg-7">
                            <div className="bd-faq-tab-content">
                                <div className="tab-content" id="pills-tabContent">
                                    <div className="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabIndex={0}>
                                        <div className="bd-faq-accordion">
                                            <div className="accordion-common-style accordion-transparent accordion-style-one">
                                                <div className="accordion" id="accordionExampleTwo">
                                                    {/* -- Question 1 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingOne">
                                                            <button className="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                How do I create an account on iStudy?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseOne" className="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExampleTwo">
                                                            <div className="accordion-body">
                                                                To create an account, click on the {"Sign Up"} button on the homepage, fill
                                                                in your details, and follow the instructions to verify your email address.
                                                                Once done, {`you'll`} have access to all our courses and features.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 2 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingTwo">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                                Is there a free trial available?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwo" className="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExampleTwo">
                                                            <div className="accordion-body">
                                                                Yes, iStudy offers a 7-day free trial for some of its courses. During
                                                                this period, you can explore course materials and resources before deciding
                                                                to purchase or enroll.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 3 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingThree">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                                What payment methods do you accept?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThree" className="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExampleTwo">
                                                            <div className="accordion-body">
                                                                We accept a variety of payment methods including credit/debit cards,
                                                                PayPal, and direct bank transfers. All payments are processed securely
                                                                through trusted gateways.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 4 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingFour">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                                Can I access courses on mobile devices?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseFour" className="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExampleTwo">
                                                            <div className="accordion-body">
                                                                Yes, iStudy’s platform is fully responsive, and you can access all our
                                                                courses on your smartphone or tablet through the mobile web browser or our
                                                                dedicated mobile app.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 5 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingFive">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                                                Do you offer any group discounts?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseFive" className="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionExampleTwo">
                                                            <div className="accordion-body">
                                                                Yes, we offer group discounts for organizations and schools enrolling
                                                                multiple students. Please contact our support team for more details on bulk
                                                                enrollment pricing.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabIndex={0}>
                                        <div className="bd-faq-accordion">
                                            <div className="accordion-common-style accordion-transparent accordion-style-one">
                                                <div className="accordion" id="accordionExampleEight">
                                                    {/* -- Question 1 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingSix">
                                                            <button className="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="true" aria-controls="collapseSix">
                                                                How do I get academic support if I struggle with a course?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseSix" className="accordion-collapse collapse show" aria-labelledby="headingSix" data-bs-parent="#accordionExampleEight">
                                                            <div className="accordion-body">
                                                                If you encounter any difficulties, you can reach out to academic support
                                                                through the course discussion forum or request a one-on-one session with
                                                                the instructor. Additionally, we offer a support team available 24/7 for
                                                                any technical or content-related queries.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 2 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingSeven">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                                                How can I upgrade my course after enrolling?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseSeven" className="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#accordionExampleEight">
                                                            <div className="accordion-body">
                                                                You can upgrade your course by logging into your account and navigating to
                                                                the {"My Courses"} section. If an upgrade option is available, you will see a
                                                                prompt to upgrade to a higher tier, granting access to additional content
                                                                or features.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 3 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingEight">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                                                How do I apply for a refund if I am unsatisfied with a course?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseEight" className="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#accordionExampleEight">
                                                            <div className="accordion-body">
                                                                Refund requests must be submitted within 14 days of enrollment. To apply,
                                                                go to the {"Support"} section in your account, select the course {`you're`}
                                                                dissatisfied with, and request a refund. The support team will process your
                                                                request promptly.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 4 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingNine">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                                                Can I access courses after completing them?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseNine" className="accordion-collapse collapse" aria-labelledby="headingNine" data-bs-parent="#accordionExampleEight">
                                                            <div className="accordion-body">
                                                                Yes, once you have completed a course, you will retain lifetime access to
                                                                the course materials, including video lectures, assignments, and additional
                                                                resources. You can revisit the content anytime to refresh your knowledge.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 5 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingTen">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                                                Is there a way to connect with other students in my course?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTen" className="accordion-collapse collapse" aria-labelledby="headingTen" data-bs-parent="#accordionExampleEight">
                                                            <div className="accordion-body">
                                                                Yes, we offer several ways to connect with fellow students, including
                                                                course-specific forums, live chat features, and group assignments. You can
                                                                collaborate with peers, share ideas, and discuss topics directly through
                                                                these platforms.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabIndex={0}>
                                        <div className="bd-faq-accordion">
                                            <div className="accordion-common-style accordion-transparent accordion-style-one">
                                                <div className="accordion" id="accordionExampleThree">
                                                    {/* -- Question 1 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingSixteen">
                                                            <button className="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSixteen" aria-expanded="true" aria-controls="collapseSixteen">
                                                                How do I purchase a course?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseSixteen" className="accordion-collapse collapse show" aria-labelledby="headingSixteen" data-bs-parent="#accordionExampleThree">
                                                            <div className="accordion-body">
                                                                To purchase a course, simply browse our catalog, select the course {`you're`}
                                                                interested in, and click on the {"Buy Now"} button. You will be prompted to
                                                                complete the payment process via a secure payment gateway.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 2 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingSeventeen">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeventeen" aria-expanded="false" aria-controls="collapseSeventeen">
                                                                What payment methods are accepted?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseSeventeen" className="accordion-collapse collapse" aria-labelledby="headingSeventeen" data-bs-parent="#accordionExampleThree">
                                                            <div className="accordion-body">
                                                                We accept a wide range of payment methods including credit and debit cards
                                                                (Visa, MasterCard, American Express), PayPal, and other secure payment
                                                                methods.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 3 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingEighteen">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEighteen" aria-expanded="false" aria-controls="collapseEighteen">
                                                                How can I request a refund for a course?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseEighteen" className="accordion-collapse collapse" aria-labelledby="headingEighteen" data-bs-parent="#accordionExampleThree">
                                                            <div className="accordion-body">
                                                                Refunds can be requested within 14 days of purchase. To request a refund,
                                                                go to your {"My Orders"} section, select the course, and click on the
                                                                {"Request Refund"} button. The refund process is handled through our support
                                                                team, and it may take up to 7 business days for the refund to be processed.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 4 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingNineteen">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNineteen" aria-expanded="false" aria-controls="collapseNineteen">
                                                                Can I get a refund if I complete the course?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseNineteen" className="accordion-collapse collapse" aria-labelledby="headingNineteen" data-bs-parent="#accordionExampleThree">
                                                            <div className="accordion-body">
                                                                Refunds are only available for courses that are not fully completed. Once a
                                                                course is 100% completed, a refund request will no longer be eligible.
                                                                Please review our refund policy for more details.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/* -- Question 5 -- */}
                                                    <div className="accordion-item">
                                                        <h2 className="accordion-header" id="headingTwenty">
                                                            <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwenty" aria-expanded="false" aria-controls="collapseTwenty">
                                                                How long does it take to receive a refund?
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwenty" className="accordion-collapse collapse" aria-labelledby="headingTwenty" data-bs-parent="#accordionExampleThree">
                                                            <div className="accordion-body">
                                                                Refunds are processed within 7-10 business days, depending on the payment
                                                                method. Once approved, the refunded amount will be credited back to your
                                                                original payment method.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="bd-faq-page-shape d-none d-xxl-block"><Image src={faqShape} alt="shape" />
                    </div>
                </div>
            </section>
            {/* -- faq area end -- */}
            <AboutCtaArea aboutWrapper={true}/>
        </>
    );
};

export default FaqMain;