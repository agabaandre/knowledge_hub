import Link from 'next/link';
import React from 'react';

const EventDetailsLocation = () => {
    return (
        <>
            <div className="bd-event-details-content">
                <h3 className="bd-details-content-title">Location</h3>
                <div className="bd-event-details-location">
                    <div className="address"><span className="icon"><i className="fa-regular fa-location-dot"></i></span> <Link href="#">Washington, DC 20001, US</Link></div>
                    <div className="address"><span className="icon"><i className="fa-light fa-envelope"></i></span> <Link href="mailto:info@istudy.com">info@istudy.com</Link></div>
                    <div className="address"><span className="icon"><i className="fa-regular fa-phone"></i></span> <Link href="tel:01234567890">+0123 - 4567890</Link></div>
                </div>
                <div className="bd-event-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2947.5026841384947!2d-71.12082372388993!3d42.374436771191476!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89e377427d7f0199%3A0x5937c65cee2427f0!2sHarvard%20University!5e0!3m2!1sen!2sbd!4v1726463965680!5m2!1sen!2sbd" width="600" height="300" style={{ border: 0 }} loading="lazy" referrerPolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </>
    );
};

export default EventDetailsLocation;