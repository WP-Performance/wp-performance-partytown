import fs from 'fs'
import { partytownSnippet } from '@builder.io/partytown/integration'

// script partytown
const snippetText = partytownSnippet()

// create file
const writeStream = fs.createWriteStream('partytown.js')

// write inside
writeStream.write(snippetText)

// close
writeStream.end()
