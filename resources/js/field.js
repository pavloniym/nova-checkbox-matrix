import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting(app => {
  app.component('index-checkbox-matrix', IndexField)
  app.component('detail-checkbox-matrix', DetailField)
  app.component('form-checkbox-matrix', FormField)
})
