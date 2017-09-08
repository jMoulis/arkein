/**
 * Created by julien on 07/09/17.
 */

/*
 * Npm import
 */
import React from 'react';
import axios from 'axios';


/*
 * Local import
 */
import Table from 'src/interviewBundle/components/Table';

/*
 * Code
 */
class App extends React.Component {
  state = {};

  loadInterviews = () => {
    // Je récupère les données et je les mets dans le state
    const user = 11;
    axios
      .get(Routing.generate('entretien_list_by_young', {id: user}))
      .then(({ data }) => {
        console.log(data.items)
      })
  };

  render() {
    console.log(this.loadInterviews());
    return(
      <div id="app">
        <Table interviews={this.loadInterviews} />
      </div>
    )
  }
}


/*
 * Export default
 */
export default App;