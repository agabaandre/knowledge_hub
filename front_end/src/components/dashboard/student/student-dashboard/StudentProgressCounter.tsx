import CountUpContent from "@/components/common/counter/CountUpContent";
import { ICounterItem } from "@/interFace/dashboard-interface";
import React from "react";

const counterData: ICounterItem[] = [
    { icon: "fa-solid fa-book-open", count: 15, text: "Total Courses Taken" },
    { icon: "fa-solid fa-user-check", count: 11, text: "Courses Enrolled" },
    { icon: "fa-solid fa-book-reader", count: 9, text: "Active Courses" },
    { icon: "fa-solid fa-check-circle", count: 95, text: "Courses Completed", symbol: "+" },
    { icon: "fa-solid fa-users", count: 595, text: "Total Students in Courses", symbol: "+" },
    { icon: "fa-solid fa-wallet", count: 95, text: "Total Fees Paid", symbol: "$" },
];

const StudentProgressCounter: React.FC = () => {
    return (
        <>
            {counterData.map((item, index) => (
                <div key={index} className="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                    <div className="bd-counter-wrapper bd-counter-style-six">
                        <div className="bd-counter-item">
                            <div className="bd-counter-content">
                                <span className="bd-counter-icon bg-two">
                                    <i className={item.icon}></i>
                                </span>
                                <h2 className="bd-counter-title">
                                    <CountUpContent number={item.count} text={item.symbol || ""} />
                                </h2>
                                <p>{item.text}</p>
                            </div>
                        </div>
                    </div>
                </div>
            ))}
        </>
    );
};

export default StudentProgressCounter;
