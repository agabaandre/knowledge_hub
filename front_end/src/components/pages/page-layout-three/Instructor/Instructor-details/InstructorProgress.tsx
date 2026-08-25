"use client";
import React from 'react';

// Define an interface for the progress item
interface ProgressItem {
    title: string;
    percentage: number;
    bgClass: string; // CSS class for background color
}

// Array of progress items
const progressData: ProgressItem[] = [
    { title: "Python", percentage: 95, bgClass: "bg-1" },
    { title: "Machine Learning", percentage: 90, bgClass: "bg-2" },
    { title: "Data Analysis", percentage: 85, bgClass: "bg-3" },
    { title: "SQL", percentage: 88, bgClass: "bg-4" },
    { title: "Artificial Intelligence", percentage: 80, bgClass: "bg-5" },
];

const InstructorProgress: React.FC = () => {
    return (
        <div className="bd-instructor-progress">
            <div className="progress-wrapper">
                {progressData.map((item, index) => (
                    <div className="progress-item" key={index}>
                        <div className='d-flex justify-content-between'>
                            <span className="title">{item.title}</span>
                            <span className="title">{item.percentage}%</span>
                        </div>
                        <div className="progress">
                            <div
                                className={`progress-bar progress-bar-striped ${item.bgClass}`}
                                role="progressbar"
                                style={{ width: `${item.percentage}%` }}
                                aria-valuenow={item.percentage}
                                aria-valuemin={0}
                                aria-valuemax={100}
                            ></div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default InstructorProgress;
