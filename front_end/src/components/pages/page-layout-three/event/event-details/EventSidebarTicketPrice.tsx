"use client";
import React from 'react';
import CountdownTimer from './CountdownTimer';

const EventSidebarTicketPrice = () => {
    return (
        <div className="bd-event-sidebar mb-30">
            <CountdownTimer targetDate="5 Aug 2025 14:00:00 GMT+06:00" />
            <div className="bd-event-info mb-20">
                <div className="bd-event-price-wrap d-flex-between">
                    <span className="title">Ticket Price</span>
                    <div className="bd-event-price">
                        <span className="current-price">$59.00</span>
                        <span className="old-price">$79.00</span>
                    </div>
                </div>
            </div>
            <div className="bd-event-info-list">
                <ul>
                    <li>
                        <div className="label">
                            <i className="fa-solid fa-calendar-days"></i> Date
                        </div>
                        <span className="value">25 Nov 2024</span>
                    </li>
                    <li>
                        <div className="label">
                            <i className="fa-solid fa-clock"></i> Schedule
                        </div>
                        <span className="value">9 AM - 3 PM</span>
                    </li>
                    <li>
                        <div className="label">
                            <i className="fa-solid fa-map-marker-alt"></i> Location
                        </div>
                        <span className="value">Washington, DC 20001, US</span>
                    </li>
                    <li>
                        <div className="label">
                            <i className="fa-solid fa-list"></i> Category
                        </div>
                        <span className="value">Education</span>
                    </li>
                    <li>
                        <div className="label">
                            <i className="fa-solid fa-globe"></i> Language
                        </div>
                        <span className="value">English</span>
                    </li>
                    <li>
                        <div className="label">
                            <i className="fa-solid fa-bookmark"></i> Estimated Seats
                        </div>
                        <span className="value">150 Seats</span>
                    </li>
                </ul>
            </div>
        </div>
    );
};

export default EventSidebarTicketPrice;
