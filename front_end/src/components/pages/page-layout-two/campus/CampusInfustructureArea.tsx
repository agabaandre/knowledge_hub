import React from "react";

interface InfrastructureData {
  building: string;
  level: string;
  area: string;
  facilities: string;
}

const infrastructureData: InfrastructureData[] = [
  { building: "Basement-2", level: "-16 ft", area: "45,223.00", facilities: "100 Car parking, Maintenance Room, Archive Storage." },
  { building: "Basement-1", level: "-8 ft", area: "40,528.00", facilities: "80 Car parking, Water Reservoir, Civil Engineering Labs, Storage Area." },
  { building: "Ground Floor", level: "+3 ft", area: "42,185.00", facilities: "Admissions Office, Cafeteria, Student Common Rooms, Security Department." },
  { building: "First Floor", level: "+13 ft", area: "37,120.00", facilities: "Administration Block, Faculty Offices, University Library, Study Rooms." },
  { building: "Second Floor", level: "+24 ft", area: "37,270.00", facilities: "Computer Labs, Lecture Halls, Conference Rooms, Faculty Lounge." },
  { building: "Third Floor", level: "+35 ft", area: "36,850.00", facilities: "Engineering Labs, Research Centers, Seminar Rooms." },
  { building: "Fourth Floor", level: "+46 ft", area: "36,068.00", facilities: "Science Labs, Department Offices, Faculty Meeting Rooms." },
  { building: "Fifth Floor", level: "+57 ft", area: "33,877.00", facilities: "Health and Wellness Center, Psychology Labs, Student Counseling Center." },
  { building: "Sixth Floor", level: "+68 ft", area: "33,853.00", facilities: "Medical Facilities, Prayer Room, Faculty Rooms, Study Hall." },
  { building: "Seventh Floor", level: "+79 ft", area: "30,785.00", facilities: "Guest Rooms, International Faculty Lounge, Lecture Halls." },
  { building: "Roof", level: "+90 ft", area: "12,500.00", facilities: "Rooftop Garden, Water Tank, Solar Panel Area, Observatory." },
];

const CampusInfrastructureArea: React.FC = () => {
  return (
    <section className="bd-infrastructure-area section-space-bottom" id="info">
      <div className="container">
        <div className="row">
          <div className="col-xl-12">
            <div className="bd-section-title-wrapper section-title-space">
              <h3 className="bd-section-title bottom-line">Infrastructure</h3>
            </div>
          </div>
        </div>
        <div className="row">
          <div className="col-xl-12">
            <div className="bd-campus-infrastructure table-responsive">
              <table className="table table-bordered w-100">
                <thead>
                  <tr>
                    <th style={{ minWidth: "164px" }}>Building</th>
                    <th style={{ minWidth: "212px" }}>Level from Ground</th>
                    <th style={{ minWidth: "156px" }}>Area in Sq. ft</th>
                    <th style={{ minWidth: "786px" }}>Facilities</th>
                  </tr>
                </thead>
                <tbody>
                  {infrastructureData.map((data, index) => (
                    <tr key={index}>
                      <td>{data.building}</td>
                      <td>{data.level}</td>
                      <td>{data.area}</td>
                      <td>{data.facilities}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default CampusInfrastructureArea;