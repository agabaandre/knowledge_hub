import { academicCalendarData } from '@/data/academic-calendar-data';
import Image from 'next/image';
import React from 'react';

const AcademicCalenderArea = () => {
    
    return (
        <>
            <section className="bd-academic-calendar-area section-space">
                <div className="container">
                    <div className="row g-30 justify-content-center">
                        {
                            academicCalendarData.map((item, index) => <div key={index} className="col-xl-10 col-lg-12 col-md-10">
                                <div className={`bd-academic-calendar-box ${index % 2 === 0 ? '' : 'row-reverse'}`}>
                                    <div className="content">
                                        <h3 className="bd-details-content-title">{item.semester}</h3>
                                        <ul className="bd-academic-calendar-list">
                                            {
                                                item.events.map((evItem, evIndex) => <li key={evIndex}><strong>{evItem.label}:</strong> {evItem.date}</li>)
                                            }
                                        </ul>
                                    </div>
                                    <div className="thumb"><Image src={item.image} style={{ width: '100%', height: 'auto' }} alt="image" /></div>
                                </div>
                            </div>)
                        }
                    </div>
                </div>
            </section>
        </>
    );
};

export default AcademicCalenderArea;