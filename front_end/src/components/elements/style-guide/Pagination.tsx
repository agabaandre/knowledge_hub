import Link from 'next/link';
import React from 'react';

const Pagination = () => {
    return (
        <>
            <div className="bd-style-guide-pagination">
                <h5 className="bd-style-guide-title mb-20">Pagination</h5>
                <div className="bd-style-pagination-item">
                    <div className="basic-pagination mt-0 text-start">
                        <nav>
                            <ul>
                                <li>
                                    <Link href="#" className="prev">
                                        <i className="fa-solid fa-angle-left"></i>
                                    </Link>
                                </li>
                                <li>
                                    <Link className="current" href="#">1</Link>
                                </li>
                                <li>
                                    <Link href="#">2</Link>
                                </li>
                                <li>
                                    <Link href="#">3</Link>
                                </li>
                                <li>
                                    <Link href="#" className="next">
                                        <i className="fa-solid fa-angle-right"></i>
                                    </Link>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </>
    );
};

export default Pagination;