"use client"
import React, { useState } from "react";

const NumberOfTicketsSection = () => {
    const [ticketCount, setTicketCount] = useState(1);

    // Increment function
    const handleIncrease = () => {
        setTicketCount(prev => {
            const newCount = prev + 1;
            return newCount;
        });
    };

    // Decrement function
    const handleDecrease = () => {
        setTicketCount(prev => {
            if (prev > 1) {
                const newCount = prev - 1;
                return newCount;
            }
            return prev;
        });
    };

    // Handle input change
    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = parseInt(e.target.value, 10);
        if (!isNaN(value) && value > 0) {
            setTicketCount(value);
        }
    }

    return (
        <div className="bd-event-sidebar mb-30">
            <div className="d-flex-between mb-20">
                <h6>Number of Tickets</h6>
                <div className="bd-event-ticket">
                    <span className="decrease" onClick={handleDecrease}>
                        <i className="fa-regular fa-minus"></i>
                    </span>
                    <input
                        className="bd-event-ticket-input"
                        type="text"
                        value={ticketCount}
                        onChange={handleInputChange}
                    />
                    <span className="increase" onClick={handleIncrease}>
                        <i className="fa-regular fa-plus"></i>
                    </span>
                </div>
            </div>
            <div className="bd-sidebar-booking">
                <form className="bd-sidebar-booking-form" action="#" method="get">
                    <div className="input-box">
                        <input type="text" placeholder="Name" />
                    </div>
                    <div className="input-box">
                        <input type="email" placeholder="Email" />
                    </div>
                    <div className="input-box">
                        <textarea cols={30} rows={10} placeholder="Type Comment here"></textarea>
                    </div>
                    <div className="booking-btn">
                        <button className="bd-btn btn-outline-border-primary w-100" type="submit">
                            Reserve Your Spot Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default NumberOfTicketsSection;
