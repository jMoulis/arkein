/*
 * Npm import
 */
import React from 'react';
import PropTypes from 'prop-types';


/*
 * Local import
 */
import Tr from './Tr';

/*
 * Code
 */
const TableBody = ({ interviews }) => (

  <tbody>
    { interviews['interviews'].map(interview => (
        <Tr key={interview.id}
            interview={interview}/>
    ))}
  </tbody>
);
TableBody.propTypes = {

};


/*
 * Export default
 */
export default TableBody;