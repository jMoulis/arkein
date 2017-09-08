/*
 * Npm import
 */
import React from 'react';
import PropTypes from 'prop-types';


/*
 * Local import
 */
import TableHeader from './TableHeader';
import TableBody from './TableBody';

/*
 * Code
 */
const Table = (interviews) => (
  <div className="js-main-content-created">
    <table className="table table-responsive table-hover">
      <TableHeader />
      <TableBody interviews={interviews}/>
    </table>
  </div>
);
Table.propTypes = {

};


/*
 * Export default
 */
export default Table;