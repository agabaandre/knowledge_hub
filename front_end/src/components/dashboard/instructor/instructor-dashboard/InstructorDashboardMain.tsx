import React from "react";
import EarningCard from "./EarningCard";
import { EarningData } from "@/interFace/dashboard-interface";
import Link from "next/link";

// Earnings data array
const earningsData: EarningData[] = [
    { icon: "fa-dollar-sign", amount: 950, label: "Total Earnings", suffix: "$" },
    { icon: "fa-wallet", amount: 9, label: "Current Balance", suffix: "$" },
    { icon: "fa-arrow-down", amount: 350, label: "Total Withdraws", suffix: "$" },
    { icon: "fa-book-open", amount: 95, label: "Active Course", suffix: "+" },
    { icon: "fa-user-graduate", amount: 15995, label: "Total Student", suffix: "+" },
    { icon: "fa-receipt", amount: 95, label: "Deducted Fees", suffix: "$" }
];

const InstructorDashboardMain: React.FC = () => {
    return (
            <div className="col-xl-9 col-lg-9 col-md-8">
                <div className="bd-dashboard-inner">
                    {/* Earnings Section */}
                    <div className="bd-dashboard-earnings-box mb-30">
                        <div className="bd-dashboard-title-inner">
                            <h4 className="bd-dashboard-title">Earnings</h4>
                        </div>
                        <div className="container p-0">
                            <div className="row gy-30 justify-content-center">
                                {earningsData.map((item, index) => (
                                    <EarningCard key={index} {...item} />
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Course List Section */}
                    <div className="bd-dashboard-course-area">
                        <div className="bd-dashboard-title-inner">
                            <h4 className="bd-dashboard-title">My Course List</h4>
                        </div>
                        <div className="bd-dashboard-course-table">
                            <div className="table-responsive">
                                <table className="table table-head-bg">
                                    <thead>
                                        <tr>
                                            <th>Course Name</th>
                                            <th>Course Duration</th>
                                            <th>Enrolled</th>
                                            <th>Ratings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {[
                                            {
                                                name: "Introduction to Computer Science",
                                                duration: "12 Weeks",
                                                enrolled: "4,500 Students",
                                                rating: "4.8/5"
                                            },
                                            {
                                                name: "Data Science with Python",
                                                duration: "10 Weeks",
                                                enrolled: "3,200 Students",
                                                rating: "4.7/5"
                                            },
                                            {
                                                name: "Business Management Basics",
                                                duration: "8 Weeks",
                                                enrolled: "5,000 Students",
                                                rating: "4.9/5"
                                            },
                                            {
                                                name: "Digital Marketing Essentials",
                                                duration: "6 Weeks",
                                                enrolled: "2,800 Students",
                                                rating: "4.6/5"
                                            },
                                            {
                                                name: "Introduction to Graphic Design",
                                                duration: "5 Weeks",
                                                enrolled: "3,500 Students",
                                                rating: "4.5/5"
                                            }
                                        ].map((course, index) => (
                                            <tr key={index}>
                                                <td>{course.name}</td>
                                                <td>{course.duration}</td>
                                                <td>{course.enrolled}</td>
                                                <td>{course.rating}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {/* Browse Courses Button */}
                    <div className="bd-more-button text-center mt-30">
                        <Link href="/courses-list-one" className="bd-btn btn-primary">
                            Browse All Courses
                        </Link>
                    </div>
                </div>
            </div>
    );
};

export default InstructorDashboardMain;
