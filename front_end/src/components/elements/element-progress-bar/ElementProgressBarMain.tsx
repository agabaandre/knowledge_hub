import ElementsBreadcrumb from '@/components/common/Breadcrumb/ElementsBreadcrumb';
import React from 'react';
import ProgressBarStyleOne from './ProgressBarStyleOne';
import ProgressBarStyleTwo from './ProgressBarStyleTwo';
import ProgressBarStyleThree from './ProgressBarStyleThree';
import ProgressBarStyleFive from './ProgressBarStyleFive';
import ProgressBarStyleFour from './ProgressBarStyleFour';

const ElementProgressBarMain = () => {
    return (
        <>
            <ElementsBreadcrumb title='Progress bar' subTitle='Progress bar style' />
            <ProgressBarStyleOne />
            <ProgressBarStyleTwo />
            <ProgressBarStyleThree />
            <ProgressBarStyleFour />
            <ProgressBarStyleFive />
        </>
    );
};

export default ElementProgressBarMain;