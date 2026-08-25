import Link from 'next/link';
import React from 'react';

const CategoryDropdown = () => {
    return (
        <>
            <ul>
                <li><Link href="/courses">Development</Link></li>
                <li><Link href="/courses">Marketing</Link></li>
                <li><Link href="/courses">Photography</Link></li>
                <li><Link href="/courses">Life Style</Link></li>
                <li><Link href="/courses">Health &amp; Fitness</Link></li>
                <li><Link href="/courses">Data Science</Link></li>
                <li><Link href="/courses">Marketing</Link></li>
                <li><Link href="/courses">Photography</Link></li>
            </ul>
        </>
    );
};

export default CategoryDropdown;