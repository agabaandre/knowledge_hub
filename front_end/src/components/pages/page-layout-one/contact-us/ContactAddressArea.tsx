import { contactData } from '@/data/contact-data';
import React from 'react';

const ContactAddressArea = () => {
    return (
        <>
            <section className="bd-contact-address-area section-space primary-bg">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6 col-lg-8">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Locations</span>
                                <h2 className="bd-section-title mb-20">Our Global Offices</h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {contactData.map((item, index) => (
                            <div key={index} className="col-xl-3 col-lg-6 col-md-6">
                                <div className="bd-contact-address-box">
                                    <div className="icon">
                                        <i className={item.icon}></i>
                                    </div>
                                    <div className="content">
                                        <h6 className="title">{item.title}</h6>
                                        {item.details.map((detail, idx) => (
                                            typeof detail === 'string' ? (
                                                <p key={idx}>{detail}</p>
                                            ) : (
                                                <p key={idx}><a href={detail.link} target="_blank" rel="noopener noreferrer">{detail.text}</a></p>
                                            )
                                        ))}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="bd-contact-map">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3168.3995816270603!2d-122.1726349236816!3d37.427664232078364!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fbb2a678bea9d%3A0x29cdf01a44fc687f!2sStanford%20University!5e0!3m2!1sen!2sbd!4v1727155102819!5m2!1sen!2sbd" width="600" height="400" style={{ border: '0' }} allowFullScreen loading="lazy" referrerPolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default ContactAddressArea;