import Link from 'next/link';
import React from 'react';

const purchaseHistoryData = [
    {
        course: "Digital Marketing Mastery SEO & Social Media",
        price: "£29.99",
        paymentStatus: "Verified",
        paymentMethod: "Master Card",
        date: "23-10-2024",
    },
    {
        course: "Healthy Cooking at Home: Nutritious Recipes",
        price: "£27.99",
        paymentStatus: "Verified",
        paymentMethod: "Amazon Pay",
        date: "23-10-2024",
    },
    {
        course: "Unlocking Insights with Data Analysis",
        price: "£19.99",
        paymentStatus: "Verified",
        paymentMethod: "American Express",
        date: "23-10-2024",
    },
    {
        course: "The Art of Web Design: Creating Digital Experiences",
        price: "£39.99",
        paymentStatus: "Verified",
        paymentMethod: "Google Pay",
        date: "23-10-2024",
    },
    {
        course: "Photography Fundamentals: From Beginner to Pro",
        price: "£44.99",
        paymentStatus: "Verified",
        paymentMethod: "American Express",
        date: "23-10-2024",
    },
];

const PurchaseHistoryMain = () => {
    return (
        <div className="col-xl-9 col-lg-9 col-md-8">
            <div className="bd-dashboard-inner">
                <div className="bd-dashboard-title-inner">
                    <h4 className="bd-dashboard-title">Purchase History</h4>
                </div>
                <div className="bd-dashboard-table table-responsive mt-30">
                    <table className="table table-bordered table-head-bg">
                        <thead>
                            <tr>
                                <th style={{ minWidth: "300px" }}>Course</th>
                                <th>Price</th>
                                <th style={{ minWidth: "140px" }}>Payment Status</th>
                                <th style={{ minWidth: "170px" }}>Payment Method</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {purchaseHistoryData.map((purchase, index) => (
                                <tr key={index}>
                                    <td>
                                        <p>{purchase.course}</p>
                                    </td>
                                    <td>
                                        <p>{purchase.price}</p>
                                    </td>
                                    <td>
                                        <div className="bd-badge badge-success">{purchase.paymentStatus}</div>
                                    </td>
                                    <td>
                                        <p>{purchase.paymentMethod}</p>
                                    </td>
                                    <td>
                                        <p>{purchase.date}</p>
                                    </td>
                                    <td>
                                        <div className="bd-button-action">
                                            <Link className="bd-default-tooltip edit" href="#">
                                                <span><i className="fa-light fa-pen-to-square"></i></span>
                                            </Link>
                                            <Link className="bd-default-tooltip view" href="#">
                                                <span><i className="fa-sharp fa-light fa-eye"></i></span>
                                            </Link>
                                            <Link className="bd-default-tooltip delete" href="#">
                                                <span><i className="fa-light fa-trash-can"></i></span>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
};

export default PurchaseHistoryMain;
