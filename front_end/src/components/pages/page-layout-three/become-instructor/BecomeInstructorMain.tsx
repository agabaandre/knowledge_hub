import React from 'react';
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import BecomeInstructorFeatures from './BecomeInstructorFeatures';
import BecomeInstructorCounter from './BecomeInstructorCounter';
import JoiningInfoArea from './JoiningInfoArea';
import JoiningFormArea from './JoiningFormArea';

const BecomeInstructorMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle="Become Instructor" />
            <BecomeInstructorFeatures />
            <BecomeInstructorCounter />
            <JoiningInfoArea/>
            <JoiningFormArea/>
        </>
    );
};

export default BecomeInstructorMain;