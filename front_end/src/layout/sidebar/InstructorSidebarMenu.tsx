"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import React from "react";

const InstructorSidebarMenu = () => {
    const pathname = usePathname();

    const menuItems = [
        { href: "/instructor-dashboard", icon: "fa-gauge-high", label: "Dashboard" },
        { href: "/instructor-profile", icon: "fa-id-badge", label: "My Profile" },
        { href: "/instructor-analytics", icon: "fa-chart-line", label: "Analytics" },
        { href: "/instructor-enrolled-courses", icon: "fa-book-reader", label: "Enrolled Courses" },
        { href: "/instructor-wishlist", icon: "fa-heart", label: "Wishlist" },
        { href: "/instructor-reviews", icon: "fa-comment-dots", label: "Reviews" },
        { href: "/instructor-purchase-history", icon: "fa-receipt", label: "Purchase History" },
    ];

    const instructorItems = [
        { href: "/instructor-courses", icon: "fa-chalkboard-user", label: "My Courses" },
        { href: "/instructor-announcements", icon: "fa-bullhorn", label: "Announcement" },
        { href: "/instructor-my-quiz-attempts", icon: "fa-file-lines", label: "My Quiz Attempts" },
        { href: "/instructor-assignments", icon: "fa-tasks", label: "Assignments" },
        { href: "/instructor-books", icon: "fa-book", label: "My Books" },
        { href: "/instructor-certificate", icon: "fa-award", label: "Certificate" },
    ];

    const userItems = [
        { href: "/instructor-settings", icon: "fa-sliders", label: "Settings" },
        { href: "/", icon: "fa-sign-out-alt", label: "Logout" },
    ];

    return (
        <div className="col-xl-3 col-lg-3 col-md-4">
            <div className="bd-dashboard-menu">
                <h6 className="bd-dashboard-menu-title mt-0">Welcome, Stefan</h6>
                <ul>
                    {menuItems.map(({ href, icon, label }) => (
                        <li key={href}>
                            <Link href={href} className={pathname === href ? "active" : ""}>
                                <span><i className={`fa-light ${icon}`}></i></span> {label}
                            </Link>
                        </li>
                    ))}
                </ul>

                <h6 className="bd-dashboard-menu-title">Instructor</h6>
                <ul>
                    {instructorItems.map(({ href, icon, label }) => (
                        <li key={href}>
                            <Link href={href} className={pathname === href ? "active" : ""}>
                                <span><i className={`fa-light ${icon}`}></i></span> {label}
                            </Link>
                        </li>
                    ))}
                </ul>

                <h6 className="bd-dashboard-menu-title">User</h6>
                <ul>
                    {userItems.map(({ href, icon, label }) => (
                        <li key={href}>
                            <Link href={href} className={pathname === href ? "active" : ""}>
                                <span><i className={`fa-light ${icon}`}></i></span> {label}
                            </Link>
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default InstructorSidebarMenu;
