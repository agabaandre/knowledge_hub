'use client'

import programData from '@/data/programe-data';
import { motion, AnimatePresence } from "framer-motion";
import Image from 'next/image';
import Link from 'next/link';
import React, { useState } from 'react';

const UniversityProgrammeArea = () => {
    const [renderedData, setRenderedData] = useState(programData.slice(0, 6));
    const [showAll, setShowAll] = useState(false);
    const [activeFilter, setActiveFilter] = useState('showAll');

    // Function to handle the filter logic based on program type (undergraduate, graduate, phd)
    const handleProgramData = (programType: string) => {
        setShowAll(false);
        setActiveFilter(programType);

        // Filter the program data based on the selected program type
        if (programType === 'undergraduate') {
            setRenderedData(programData.filter(data => data.program === 'undergraduate'));
        } else if (programType === 'graduate') {
            setRenderedData(programData.filter(data => data.program === 'graduate'));
        } else if (programType === 'phd') {
            setRenderedData(programData.filter(data => data.program === 'phd'));
        } else {
            // If 'showAll' is selected, show the first 6 programs
            setRenderedData(programData.slice(0, 6));
        }
    };

    // Toggle function to show all programs or revert back to showing the first 6 programs
    const handleShowAll = () => {
        setShowAll(!showAll);
        setActiveFilter('showAll');
        // Update renderedData based on whether 'showAll' is true or false
        setRenderedData(showAll ? programData.slice(0, 6) : programData.slice(0, 6));
    };

    // Animation variants for the program card container
    const containerVariants = {
        hidden: { opacity: 0, y: 50 },
        visible: {
            opacity: 1,
            y: 0,
            transition: { 
                duration: 0.5, 
                ease: [0.16, 1, 0.3, 1] as const,
            },
        },
    };

    return (
        <section className="bd-program-area section-space-bottom">
            <div className="container">
                <div className="row gy-30 justify-content-between align-items-center section-title-space">
                    <div className="col-xxl-5 col-xl-5 col-lg-5">
                        <div className="bd-section-title-wrapper">
                            <span className="bd-section-subtitle">Leading Programs</span>
                            <h2 className="bd-section-title">Top University <span className="down-mark-line">Programs</span></h2>
                        </div>
                    </div>
                    <div className="col-xxl-7 col-xl-7 col-lg-7">
                        <div className="course-item text-center d-flex flex-wrap gap-15 justify-content-lg-end justify-content-start">
                            {/* Filter buttons for program types */}
                            <button
                                className={`filter-item bd-btn btn-outline-primary ${activeFilter === 'showAll' ? 'active' : ''}`}
                                onClick={handleShowAll}
                            >
                                Show All
                            </button>
                            <button
                                className={`filter-item bd-btn btn-outline-primary ${activeFilter === 'undergraduate' ? 'active' : ''}`}
                                onClick={() => handleProgramData('undergraduate')}
                            >
                                Undergraduate
                            </button>
                            <button
                                className={`filter-item bd-btn btn-outline-primary ${activeFilter === 'graduate' ? 'active' : ''}`}
                                onClick={() => handleProgramData('graduate')}
                            >
                                Graduates
                            </button>
                            <button
                                className={`filter-item bd-btn btn-outline-primary ${activeFilter === 'phd' ? 'active' : ''}`}
                                onClick={() => handleProgramData('phd')}
                            >
                                Phd Program
                            </button>
                        </div>
                    </div>
                </div>

                {/* Displaying the program cards */}
                <div className="row g-30 grid">
                    <AnimatePresence>
                        {renderedData.length > 0 && renderedData.map((item, index) => (
                            <motion.div
                                variants={containerVariants}
                                key={index}
                                className="col-xl-4 col-lg-6 col-md-6 col-sm-6 grid-item cat1"
                                initial="hidden"
                                animate="visible"
                                exit={{ opacity: 0, y: 20 }}
                            >
                                <div className="bd-course-wrapper style-six">
                                    {/* Program Card - Link to program details */}
                                    <Link href={`/program-details/${item.id}`} className="bd-course-thumb-wrapper bd-course-thumb-style-two p-relative">
                                        <div className="bd-course-thumb">
                                            <Image src={item.image} style={{ width: 'auto', height: 'auto' }} alt="image" />
                                        </div>
                                        <div className="bd-course-shape">
                                            <Image src={item.shapeImage} alt="shape" />
                                        </div>
                                        <div className="bd-course-logo">
                                            {item.textImage && <Image src={item.textImage} style={{ width: 'auto', height: 'auto' }} alt="logo" />}
                                        </div>
                                        <div className="bd-course-badge">
                                            <span className="bd-badge badge-primary">{item.type}</span>
                                        </div>
                                    </Link>
                                    <div className="bd-course-content">
                                        <h5 className="bd-course-title underline mb-15">
                                            <Link href={`/program-details/${item.id}`}>{item.title}</Link>
                                        </h5>
                                        <div className="bd-course-content-body">
                                            <p>{item.description}</p>
                                        </div>
                                        <div className="bd-course-divider"></div>
                                        <div className="bd-course-content-bottom d-flex-between flex-wrap gap-15">
                                            {/* Program Duration and Credits */}
                                            <div className="bd-course-lesson">
                                                <span><i className="fa-light fa-clock"></i> {item.duration}</span>
                                            </div>
                                            <div className="bd-course-lesson">
                                                <span><i className="fa-sharp fa-solid fa-list"></i> {item.credits}</span>
                                            </div>
                                            <Link className="bd-btn btn-outline-primary" href={`/program-details/${item.id}`}>
                                                Apply now
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </motion.div>
                        ))}
                    </AnimatePresence>
                </div>

                {/* Button to explore more programs */}
                <div className="bd-program-btn d-flex justify-content-center mt-50">
                    <Link href="/courses-grid-two" className="bd-btn btn-primary">
                        Explore More University Programs
                    </Link>
                </div>
            </div>
        </section>
    );
};

export default UniversityProgrammeArea;

