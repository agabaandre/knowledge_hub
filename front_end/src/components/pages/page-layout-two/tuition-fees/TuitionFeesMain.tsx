import BreadcrumbsTwo from '@/components/common/Breadcrumb/BreadcrumbsTwo';
import React from 'react';
import TuitionFeesArea from './TuitionFeesArea';

const TuitionFeesMain = () => {
    return (
        <>
            <BreadcrumbsTwo breadcrumbTwoTitle='Tuition and Other Fees' />
            <TuitionFeesArea />
        </>
    );
};

export default TuitionFeesMain;