import Link from 'next/link';
import React from 'react';

const BasicPagination = () => {
    return (
        <>
            <div className="basic-pagination">
                <nav>
                    <ul>
                        <li>
                            <Link href="#" className="prev">
                                <i className="fa-solid fa-angle-left"></i>
                            </Link>
                        </li>
                        <li>
                            <Link href="#" className="current">1</Link>
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
        </>
    );
};

export default BasicPagination;