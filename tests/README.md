TESTING
=======


First, get set the **tokens** in ```config.inc.php```  
More info on tokens: https://developer.surveymonkey.com/mashery/guide_oauth#tokens

The following steps assume tabula rasa.  If you already have some survey with data you want to use,  just set the **survey_id** in ```config.inc.php```, and skip the following steps. 

1. Create a *test* survey on the linked surveymonkey account
2. run ```create_collector.php``` test
3. Add question(s) to the survey *(via surveymonkey admin)*
4. Go and answer once  *(via the collector link)*
5. Run the ```getters.php``` test,  all method should successfully return data.


