import Image from 'next/image';
import React from 'react';
import contactThumb from '../../../../../public/assets/images/contact/contact-thumb.webp';
import ContactForm from '@/form/ContactForm';

const ContactFormArea = () => {
    return (
        <>
            <section className="bd-contact-form-area section-space">
                <div className="container">
                    <div className="row gy-30 justify-content-between align-items-center">
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-contact-form-wrapper">
                                <div className="bd-section-title-wrapper section-title-space">
                                    <h2 className="bd-section-title mb-20">Get in touch</h2>
                                    <p className="bd-section-paragraph">Our friendly team would love to hear from you.</p>
                                </div>
                                <div className="bd-contact-form">
                                    <ContactForm />
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-contact-form-thumb"><Image src={contactThumb} priority style={{ width: '100%', height: '100%' }} alt="images" /></div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default ContactFormArea;