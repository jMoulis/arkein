/*
 * Npm import
 */
import React from 'react';
import PropTypes from 'prop-types';


/*
 * Local import
 */


/*
 * Code
 */
const TableHead = () => (
  <thead>
    <tr>
      <th colSpan="2">Actions
        <a id="action_popover" href="#"
           tabIndex="0"
           data-trigger="focus"
           data-toggle="popover" title="Légende"
        >
          <i className="fa fa-question-circle-o" aria-hidden="true" title="Légende"/>
        </a>
      </th>
      <th>Date</th>
      <th>Objet</th>
      <th>Ordre du jour</th>
      <th>Organisateur</th>
      <th>Jeune</th>
    </tr>
  </thead>
);
TableHead.propTypes = {

};


/*
 * Export default
 */
export default TableHead;