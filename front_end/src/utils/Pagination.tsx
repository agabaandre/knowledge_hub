import Link from 'next/link';
import React from 'react';

const Pagination = () => {
    return (
        <div className="basic-pagination mt-20 mb-30">
            <ul>
                <li><Link className="prev page-numbers" href="#">
                    <i className="fal fa-angle-left"></i>
                </Link>
                </li>
                <li><span aria-current="page" className="page-numbers current">01</span></li>
                <li><Link className="page-numbers" href="#">02</Link></li>
                <li><Link className="next page-numbers" href="#">
                    <i className="fal fa-angle-right"></i>
                </Link>
                </li>
            </ul>
        </div>
    );
};

export default Pagination;