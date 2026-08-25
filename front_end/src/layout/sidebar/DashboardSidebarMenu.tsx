"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import React from "react";

const DashboardSidebarMenu = () => {
    const pathname = usePathname();

    const menuItems = [
        { href: "/student-dashboard", icon: "fa-gauge-high", label: "Dashboard" },
        { href: "/student-profile", icon: "fa-id-badge", label: "My Profile" },
        { href: "/student-analytics", icon: "fa-chart-line", label: "Analytics" },
        { href: "/student-enrolled-courses", icon: "fa-book-reader", label: "Enrolled Courses" },
        { href: "/student-books", icon: "fa-book", label: "My Books" },
        { href: "/student-wishlist", icon: "fa-heart", label: "Wishlist" },
        { href: "/student-my-quiz-attempts", icon: "fa-file-lines", label: "My Quiz Attempts" },
        { href: "/student-assignments", icon: "fa-tasks", label: "Assignments" },
        { href: "/student-reviews", icon: "fa-comment-dots", label: "Reviews" },
        { href: "/student-purchase-history", icon: "fa-receipt", label: "Purchase History" },
        { href: "/student-announcements", icon: "fa-bullhorn", label: "Announcement" },
        { href: "/student-certificate", icon: "fa-award", label: "My Achievement" },
    ];

    const userItems = [
        { href: "/student-settings", icon: "fa-sliders", label: "Settings" },
        { href: "/", icon: "fa-sign-out-alt", label: "Logout" },
    ];

    return (
        <div className="col-xl-3 col-lg-3 col-md-4">
            <div className="bd-dashboard-menu">
                <h6 className="bd-dashboard-menu-title mt-0">Welcome, Smith</h6>
                <ul>
                    {menuItems.map(({ href, icon, label }) => (
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

export default DashboardSidebarMenu;
