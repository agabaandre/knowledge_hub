import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import React from 'react';
import ProgramDetailsSliderArea from './ProgramDetailsSliderArea';
import ProgramCardArea from './ProgramCardArea';
import WayToLearnAbout from './WayToLearnAbout';
import ProgramRoutineArea from './ProgramRoutineArea';
import InformationArea from './InformationArea';
import CtaAreaStart from './CtaAreaStart';
import programData from '@/data/programe-data';

const KindergartenProgramDetailsMain = ({ id }: { id: number }) => {
    const program = programData.find((item) => item.id == id);
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Program Details Kindergarten'/>
            <ProgramDetailsSliderArea program={program}/>
            <ProgramCardArea/>
            <WayToLearnAbout/>
            <ProgramRoutineArea/>
            <InformationArea/>
            <CtaAreaStart/>
        </>
    );
};

export default KindergartenProgramDetailsMain;