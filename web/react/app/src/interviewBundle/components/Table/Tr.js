/*
 * Npm import
 */
import React from 'react';
import PropTypes from 'prop-types';


/*
 * Local import
 */
import Td from './Td';

/*
 * Code
 */
const Tr = ({ interview }) => (
  <tr>
      <td>
        <a href="#" className="js-saisir-compteRendu btn btn-warning btn-sm"
             data-id="<%= id %>"
             data-toggle="modal"
             data-target="#saisirCompteRenduModal"
             title="Saisir Compte-Rendu"
      >
        <i className="fa fa-pencil-square-o" aria-hidden="true" />
      </a></td>
      <td> <a href="<%= links._self %>"
              className="js-detail-entretien btn btn-view btn-sm"
              data-id="<%= id %>"
              data-toggle="modal"
              data-backdrop = "false"
              data-target="#editEntretienModal"
              title="Consulter">
        <i className="fa fa-eye" aria-hidden="true" />
      </a></td>
      <td>{interview.date}</td>
      <td>{interview.objet}</td>
      <td>{interview.odj}</td>
      <td>{interview.author}</td>
      <td>{interview.young}</td>
  </tr>
);
Tr.propTypes = {

};


/*
 * Export default
 */
export default Tr;